<?php

namespace App\Jobs;

use App\Models\Estacao;
use App\Models\RadioLink;
use App\Models\RadioLinkImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessRadioLinkImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    /**
     * Quantidade de linhas processadas antes de dar um flush em lote.
     */
    private const LOTE = 1000;

    public function __construct(public readonly int $importId) {}

    public function handle(): void
    {
        $import = RadioLinkImport::findOrFail($this->importId);

        if ($import->status === RadioLinkImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => RadioLinkImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => RadioLinkImport::STATUS_FALHOU,
                'erro' => __('O arquivo da importação não foi encontrado.'),
            ]);

            return;
        }

        $estacoesPorSite = Estacao::pluck('id', 'site_id')->all();

        $reader = $this->readerPara($import->arquivo);
        $reader->open($caminho);

        $totalLinhas = 0;
        $processadas = 0;
        $ignoradas = 0;
        $lote = [];
        $mapeamento = null;
        $primeirosErros = [];

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($row->isEmpty()) {
                        continue;
                    }

                    $valores = $this->normalizarValores($row);
                    $totalLinhas++;

                    if ($mapeamento === null) {
                        $mapeamento = $this->mapearColunas($valores);

                        continue;
                    }

                    $dados = $this->montarDados($valores, $mapeamento, $estacoesPorSite);

                    if ($dados === null) {
                        $ignoradas++;

                        continue;
                    }

                    $lote[] = $dados;
                    $processadas++;

                    if (count($lote) >= self::LOTE) {
                        $this->gravarLote($import, $lote);
                        $import->refresh();
                    }
                }
            }

            if ($lote !== []) {
                $this->gravarLote($import, $lote);
                $import->refresh();
            }

            $import->update([
                'status' => RadioLinkImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
                'erro' => $primeirosErros === [] ? null : implode("\n", array_slice($primeirosErros, 0, 10)),
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => RadioLinkImport::STATUS_FALHOU,
                'erro' => $e->getMessage(),
            ]);
        } finally {
            $reader->close();
        }
    }

    /**
     * @param  array<int, string|null>  $valores
     * @return array<string, int>
     */
    private function mapearColunas(array $valores): array
    {
        $mapa = [];
        $sinonimos = $this->sinonimosColunas();

        foreach ($valores as $indice => $valor) {
            if ($valor === null) {
                continue;
            }

            $normalizado = $this->normalizarCabecalho($valor);

            if (isset($sinonimos[$normalizado])) {
                $mapa[$sinonimos[$normalizado]] = $indice;
            }
        }

        return $mapa;
    }

    /**
     * @return array<string, string>
     */
    private function sinonimosColunas(): array
    {
        return [
            'codigo' => 'codigo',
            'radio_link' => 'codigo',
            'nome' => 'nome',
            'estacao_a' => 'estacao_a',
            'estacao a' => 'estacao_a',
            'site a' => 'estacao_a',
            'site_a' => 'estacao_a',
            'estacao_b' => 'estacao_b',
            'estacao b' => 'estacao_b',
            'site b' => 'estacao_b',
            'site_b' => 'estacao_b',
            'frequencia' => 'frequencia',
            'frequencia_ghz' => 'frequencia',
            'capacidade' => 'capacidade',
            'canal' => 'canal',
            'polarizacao' => 'polarizacao',
            'fabricante' => 'fabricante',
            'modelo' => 'modelo',
            'distancia' => 'distancia',
            'distancia_km' => 'distancia',
            'status' => 'status',
            'data_ativacao' => 'data_ativacao',
            'data de ativacao' => 'data_ativacao',
            'ativacao' => 'data_ativacao',
            'observacao' => 'observacao',
        ];
    }

    private function normalizarCabecalho(string $valor): string
    {
        $valor = mb_strtolower($valor);

        $acentos = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ];

        $valor = strtr($valor, $acentos);

        return trim(preg_replace('/\s+/', ' ', $valor) ?? $valor);
    }

    /**
     * @param  array<int, string|null>  $valores
     * @param  array<string, int>  $mapeamento
     * @param  array<string, int>  $estacoesPorSite
     * @return array<string, mixed>|null
     */
    private function montarDados(array $valores, array $mapeamento, array $estacoesPorSite): ?array
    {
        $codigo = $this->campo($valores, $mapeamento, 'codigo');

        if ($codigo === null) {
            return null;
        }

        $estacaoASite = $this->campo($valores, $mapeamento, 'estacao_a');
        $estacaoBSite = $this->campo($valores, $mapeamento, 'estacao_b');

        if ($estacaoASite === null || $estacaoBSite === null || $estacaoASite === $estacaoBSite) {
            return null;
        }

        $estacaoAId = $estacoesPorSite[$estacaoASite] ?? null;
        $estacaoBId = $estacoesPorSite[$estacaoBSite] ?? null;

        if ($estacaoAId === null || $estacaoBId === null) {
            return null;
        }

        return [
            'codigo' => $codigo,
            'nome' => $this->campo($valores, $mapeamento, 'nome'),
            'estacao_a_id' => $estacaoAId,
            'estacao_b_id' => $estacaoBId,
            'frequencia' => $this->decimal($this->campo($valores, $mapeamento, 'frequencia')),
            'capacidade' => $this->campo($valores, $mapeamento, 'capacidade'),
            'canal' => $this->campo($valores, $mapeamento, 'canal'),
            'polarizacao' => $this->enum($this->campo($valores, $mapeamento, 'polarizacao'), RadioLink::POLARIZACOES),
            'fabricante' => $this->enum($this->campo($valores, $mapeamento, 'fabricante'), RadioLink::FABRICANTES),
            'modelo' => $this->campo($valores, $mapeamento, 'modelo'),
            'distancia' => $this->decimal($this->campo($valores, $mapeamento, 'distancia')),
            'status' => $this->enum($this->campo($valores, $mapeamento, 'status'), RadioLink::STATUS),
            'data_ativacao' => $this->data($this->campo($valores, $mapeamento, 'data_ativacao')),
            'observacao' => $this->campo($valores, $mapeamento, 'observacao'),
        ];
    }

    /**
     * @param  array<int, string|null>  $valores
     * @param  array<string, int>  $mapeamento
     */
    private function campo(array $valores, array $mapeamento, string $campo): ?string
    {
        $indice = $mapeamento[$campo] ?? null;

        if ($indice === null) {
            return null;
        }

        $valor = $valores[$indice] ?? null;

        if ($valor === null || trim($valor) === '') {
            return null;
        }

        return trim($valor);
    }

    private function readerPara(string $arquivo): CsvReader|XlsxReader
    {
        $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

        if ($extensao === 'csv') {
            return new CsvReader(new Options(
                FIELD_DELIMITER: ';',
            ));
        }

        return new XlsxReader;
    }

    /**
     * @param  array<int, string|null>  $valores
     * @return array<int, string|null>
     */
    private function normalizarValores(Row $row): array
    {
        $valores = [];
        foreach ($row->toArray() as $valor) {
            $valores[] = $valor === null ? null : (string) $valor;
        }

        return $valores;
    }

    private function decimal(?string $valor): ?float
    {
        if ($valor === null) {
            return null;
        }

        $valor = str_replace(',', '.', $valor);

        if (! is_numeric($valor)) {
            return null;
        }

        return (float) $valor;
    }

    private function enum(?string $valor, array $opcoes): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valorNormalizado = strtoupper(trim($valor));

        foreach ($opcoes as $opcao) {
            if (strtoupper($opcao) === $valorNormalizado) {
                return $opcao;
            }
        }

        return null;
    }

    private function data(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d', 'm/d/Y'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, $valor)->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lote
     */
    private function gravarLote(RadioLinkImport $import, array &$lote): void
    {
        if ($lote === []) {
            return;
        }

        RadioLink::upsert(
            $lote,
            ['codigo'],
            ['nome', 'estacao_a_id', 'estacao_b_id', 'frequencia', 'capacidade',
                'canal', 'polarizacao', 'fabricante', 'modelo', 'distancia',
                'status', 'data_ativacao', 'observacao'],
        );

        $import->increment('importadas', count($lote));

        $lote = [];
    }
}
