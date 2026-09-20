<?php

namespace App\Jobs;

use App\Models\Servico;
use App\Models\ServicoImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessServicoImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    private const LOTE = 500;

    public function __construct(public readonly int $importId) {}

    public function handle(): void
    {
        $import = ServicoImport::findOrFail($this->importId);

        if ($import->status === ServicoImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => ServicoImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => ServicoImport::STATUS_FALHOU,
                'erro' => __('O arquivo da importação não foi encontrado.'),
            ]);

            return;
        }

        $reader = $this->readerPara($import->arquivo);
        $reader->open($caminho);

        $totalLinhas = 0;
        $processadas = 0;
        $ignoradas = 0;
        $lote = [];
        $mapeamento = null;

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

                        if ($mapeamento === []) {
                            throw new \RuntimeException(__('Não foi possível identificar as colunas do arquivo. Verifique o cabeçalho.'));
                        }

                        continue;
                    }

                    $dados = $this->montarDados($valores, $mapeamento);

                    if ($dados === null) {
                        $ignoradas++;

                        continue;
                    }

                    $lote[] = $dados;
                    $processadas++;

                    if (count($lote) >= self::LOTE) {
                        $ignoradas += $this->gravarLote($lote);
                        $import->refresh();
                    }
                }
            }

            if ($lote !== []) {
                $ignoradas += $this->gravarLote($lote);
                $import->refresh();
            }

            $import->update([
                'status' => ServicoImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => ServicoImport::STATUS_FALHOU,
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
            'nome' => 'nome',
            'servico' => 'nome',
            'codigo' => 'codigo',
            'sku' => 'codigo',
            'categoria' => 'categoria',
            'descricao' => 'descricao',
            'preco' => 'preco',
            'valor' => 'preco',
            'status' => 'ativo',
            'situacao' => 'ativo',
            'ativo' => 'ativo',
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

        $valor = preg_replace('/[^a-z0-9_]/', '_', $valor) ?? $valor;
        $valor = preg_replace('/_+/', '_', $valor) ?? $valor;

        return trim($valor, '_');
    }

    /**
     * @param  array<int, string|null>  $valores
     * @param  array<string, int>  $mapeamento
     * @return array<string, mixed>|null
     */
    private function montarDados(array $valores, array $mapeamento): ?array
    {
        $nome = $this->campo($valores, $mapeamento, 'nome');

        if ($nome === null) {
            return null;
        }

        return [
            'nome' => $nome,
            'codigo' => $this->campo($valores, $mapeamento, 'codigo'),
            'categoria' => $this->montarCategoria($this->campo($valores, $mapeamento, 'categoria')),
            'descricao' => $this->campo($valores, $mapeamento, 'descricao'),
            'preco' => $this->preco($this->campo($valores, $mapeamento, 'preco')),
            'ativo' => $this->montarAtivo($this->campo($valores, $mapeamento, 'ativo')),
        ];
    }

    private function montarCategoria(?string $categoria): string
    {
        $texto = $this->normalizarCabecalho($categoria ?? '');

        $mapa = [
            'instalacao' => 'Instalação',
            'manutencao' => 'Manutenção',
            'suporte' => 'Suporte',
            'configuracao' => 'Configuração',
            'config' => 'Configuração',
        ];

        return $mapa[$texto] ?? 'Suporte';
    }

    private function montarAtivo(?string $status): bool
    {
        if ($status === null) {
            return true;
        }

        $texto = strtolower(trim($status));

        return ! in_array($texto, ['inativo', 'inactivo', 'desativado', 'não', 'nao', 'false', '0'], true);
    }

    private function preco(?string $valor): ?float
    {
        if ($valor === null) {
            return null;
        }

        $valor = str_replace(['R$', ' ', '.'], '', $valor);
        $valor = str_replace(',', '.', $valor);

        $numero = (float) $valor;

        return $numero >= 0 ? $numero : null;
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
     * @param  array<int, array<string, mixed>>  $lote
     */
    private function gravarLote(array &$lote): int
    {
        if ($lote === []) {
            return 0;
        }

        $agora = now();
        $importadas = 0;
        $ignoradas = 0;

        foreach ($lote as $dados) {
            if ($dados['codigo'] !== null && Servico::where('codigo', $dados['codigo'])->exists()) {
                $ignoradas++;

                continue;
            }

            Servico::create(array_merge($dados, [
                'created_at' => $agora,
                'updated_at' => $agora,
            ]));

            $importadas++;
        }

        if ($importadas > 0) {
            ServicoImport::where('id', $this->importId)->increment('importadas', $importadas);
        }

        $lote = [];

        return $ignoradas;
    }
}
