<?php

namespace App\Jobs;

use App\Models\Cliente;
use App\Models\ClienteImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessClienteImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    private const LOTE = 500;

    public function __construct(public readonly int $importId) {}

    public function handle(): void
    {
        $import = ClienteImport::findOrFail($this->importId);

        if ($import->status === ClienteImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => ClienteImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => ClienteImport::STATUS_FALHOU,
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
                'status' => ClienteImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => ClienteImport::STATUS_FALHOU,
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
            'razao_social' => 'nome',
            'razao social' => 'nome',
            'empresa' => 'nome',
            'email' => 'email',
            'e-mail' => 'email',
            'cnpj' => 'documento',
            'cpnj' => 'documento',
            'documento' => 'documento',
            'telefone' => 'telefone',
            'phone' => 'telefone',
            'segmento' => 'segmento',
            'categoria' => 'segmento',
            'cidade' => 'cidade',
            'estado' => 'estado',
            'uf' => 'estado',
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
        $email = $this->campo($valores, $mapeamento, 'email');
        $documento = $this->limparCnpj($this->campo($valores, $mapeamento, 'documento'));

        if ($nome === null || $email === null || $documento === null) {
            return null;
        }

        return [
            'nome' => $nome,
            'email' => $email,
            'documento' => $documento,
            'telefone' => $this->campo($valores, $mapeamento, 'telefone'),
            'segmento' => $this->campo($valores, $mapeamento, 'segmento'),
            'cidade' => $this->campo($valores, $mapeamento, 'cidade'),
            'estado' => $this->campo($valores, $mapeamento, 'estado'),
            'ativo' => $this->montarAtivo($this->campo($valores, $mapeamento, 'ativo')),
        ];
    }

    private function montarAtivo(?string $status): bool
    {
        if ($status === null) {
            return true;
        }

        $texto = strtolower(trim($status));

        return ! in_array($texto, ['inativo', 'inactivo', 'desativado', 'não', 'nao', 'false', '0'], true);
    }

    private function limparCnpj(?string $cnpj): ?string
    {
        if ($cnpj === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $cnpj);

        if ($digits === null || strlen($digits) < 14) {
            return null;
        }

        return substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8, 4).'-'.substr($digits, 12, 2);
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
            $email = mb_strtolower($dados['email']);

            if (Cliente::where('email', $email)->exists()) {
                $ignoradas++;

                continue;
            }

            if (Cliente::where('documento', $dados['documento'])->exists()) {
                $ignoradas++;

                continue;
            }

            Cliente::create(array_merge($dados, [
                'email' => $email,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]));

            $importadas++;
        }

        if ($importadas > 0) {
            ClienteImport::where('id', $this->importId)->increment('importadas', $importadas);
        }

        $lote = [];

        return $ignoradas;
    }
}
