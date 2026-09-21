<?php

namespace App\Jobs;

use App\Models\Estacao;
use App\Models\EstacaoImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Comment\TextRun;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessEstacaoImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    private const LOTE = 500;

    public function __construct(public readonly int $importId) {}

    public function handle(): void
    {
        $import = EstacaoImport::findOrFail($this->importId);

        if ($import->status === EstacaoImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => EstacaoImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => EstacaoImport::STATUS_FALHOU,
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
                'status' => EstacaoImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => EstacaoImport::STATUS_FALHOU,
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
            'site_id' => 'site_id',
            'site' => 'site_id',
            'endereco_id' => 'endereco_id',
            'end_id' => 'endereco_id',
            'tecnologia' => 'tecnologia',
            'operadora' => 'operadora',
            'tipo_conexao' => 'tipo_conexao',
            'tipo_de_conexao' => 'tipo_conexao',
            'station_id' => 'station_id',
            'station' => 'station_id',
            'municipio' => 'municipio',
            'cidade' => 'municipio',
            'estado' => 'estado',
            'uf' => 'estado',
            'cep' => 'cep',
            'regional' => 'regional',
            'status' => 'status',
            'situacao' => 'status',
            'detentor_area' => 'detentor_area',
            'detentor_da_area' => 'detentor_area',
            'detentor' => 'detentor_area',
            'tipo_infra' => 'tipo_infra',
            'tipo_de_infra' => 'tipo_infra',
            'tipo_ev' => 'tipo_ev',
            'tipo_de_ev' => 'tipo_ev',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'tipo_logradouro' => 'tipo_logradouro',
            'logradouro' => 'logradouro',
            'numero' => 'numero',
            'complemento' => 'complemento',
            'bairro' => 'bairro',
            'tipo_torre' => 'tipo_torre',
            'aev_nominal' => 'aev_nominal',
            'area_solo' => 'area_solo',
            'altura_estrutura' => 'altura_estrutura',
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
        $siteId = $this->campo($valores, $mapeamento, 'site_id');

        if ($siteId === null) {
            return null;
        }

        return [
            'site_id' => $siteId,
            'endereco_id' => $this->campo($valores, $mapeamento, 'endereco_id'),
            'tecnologia' => $this->campo($valores, $mapeamento, 'tecnologia'),
            'operadora' => $this->campo($valores, $mapeamento, 'operadora'),
            'tipo_conexao' => $this->campo($valores, $mapeamento, 'tipo_conexao'),
            'station_id' => $this->campo($valores, $mapeamento, 'station_id'),
            'municipio' => $this->campo($valores, $mapeamento, 'municipio'),
            'estado' => $this->campo($valores, $mapeamento, 'estado'),
            'cep' => $this->campo($valores, $mapeamento, 'cep'),
            'regional' => $this->campo($valores, $mapeamento, 'regional'),
            'status' => $this->campo($valores, $mapeamento, 'status'),
            'detentor_area' => $this->campo($valores, $mapeamento, 'detentor_area'),
            'tipo_infra' => $this->campo($valores, $mapeamento, 'tipo_infra'),
            'tipo_ev' => $this->campo($valores, $mapeamento, 'tipo_ev'),
            'latitude' => $this->campo($valores, $mapeamento, 'latitude'),
            'longitude' => $this->campo($valores, $mapeamento, 'longitude'),
            'tipo_logradouro' => $this->campo($valores, $mapeamento, 'tipo_logradouro'),
            'logradouro' => $this->campo($valores, $mapeamento, 'logradouro'),
            'numero' => $this->campo($valores, $mapeamento, 'numero'),
            'complemento' => $this->campo($valores, $mapeamento, 'complemento'),
            'bairro' => $this->campo($valores, $mapeamento, 'bairro'),
            'tipo_torre' => $this->campo($valores, $mapeamento, 'tipo_torre'),
            'aev_nominal' => $this->campo($valores, $mapeamento, 'aev_nominal'),
            'area_solo' => $this->campo($valores, $mapeamento, 'area_solo'),
            'altura_estrutura' => $this->campo($valores, $mapeamento, 'altura_estrutura'),
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

    /**
     * @return array<int, string|null>
     */
    private function normalizarValores(Row $row): array
    {
        $valores = [];
        foreach ($row->toArray() as $valor) {
            $valores[] = $this->valorParaString($valor);
        }

        return $valores;
    }

    private function valorParaString(mixed $valor): ?string
    {
        return match (true) {
            $valor === null => null,
            $valor instanceof \DateTimeInterface => $valor->format('Y-m-d H:i:s'),
            is_bool($valor) => $valor ? '1' : '0',
            is_array($valor) => implode(' ', array_map(
                fn (mixed $parte): string => $parte instanceof TextRun ? $parte->text : (string) $parte,
                $valor,
            )),
            default => (string) $valor,
        };
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
            if (Estacao::where('site_id', $dados['site_id'])->exists()) {
                $ignoradas++;

                continue;
            }

            Estacao::create(array_merge($dados, [
                'created_at' => $agora,
                'updated_at' => $agora,
            ]));

            $importadas++;
        }

        if ($importadas > 0) {
            EstacaoImport::where('id', $this->importId)->increment('importadas', $importadas);
        }

        $lote = [];

        return $ignoradas;
    }
}
