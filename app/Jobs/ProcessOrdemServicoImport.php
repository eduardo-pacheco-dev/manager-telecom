<?php

namespace App\Jobs;

use App\Models\Cliente;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\OrdemServicoImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Comment\TextRun;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessOrdemServicoImport implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    /**
     * Quantidade de linhas processadas antes de dar um flush em lote.
     */
    private const LOTE = 500;

    public function __construct(public readonly int $importId) {}

    public function handle(): void
    {
        $import = OrdemServicoImport::findOrFail($this->importId);

        if ($import->status === OrdemServicoImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => OrdemServicoImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => OrdemServicoImport::STATUS_FALHOU,
                'erro' => __('O arquivo da importação não foi encontrado.'),
            ]);

            return;
        }

        $estacoesPorSite = Estacao::pluck('id', 'site_id')->all();

        $clientesPorNome = Cliente::pluck('id', 'nome')->all();

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

                    $dados = $this->montarDados($valores, $mapeamento, $estacoesPorSite, $clientesPorNome);

                    if ($dados === null) {
                        $ignoradas++;

                        continue;
                    }

                    $lote[] = $dados;
                    $processadas++;

                    if (count($lote) >= self::LOTE) {
                        $this->gravarLote($lote);
                        $import->refresh();
                    }
                }
            }

            if ($lote !== []) {
                $this->gravarLote($lote);
                $import->refresh();
            }

            $import->update([
                'status' => OrdemServicoImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => OrdemServicoImport::STATUS_FALHOU,
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
            'cod_afl' => 'codigo',
            'codafl' => 'codigo',
            'codigo' => 'codigo',
            'codigo_personalizado' => 'codigo_personalizado',
            'cod personalizado' => 'codigo_personalizado',
            'codigo_cliente' => 'codigo_cliente',
            'cod cliente' => 'codigo_cliente',
            'cliente' => 'cliente',
            'nome_cliente' => 'cliente',
            'ordem_complexa' => 'ordem_complexa',
            'status_geral' => 'status',
            'site_id_a' => 'estacao_a',
            'site a' => 'estacao_a',
            'site_a' => 'estacao_a',
            'site_id_b' => 'estacao_b',
            'site b' => 'estacao_b',
            'site_b' => 'estacao_b',
            'end_id_a' => 'end_id_a',
            'end_id_b' => 'end_id_b',
            'projeto' => 'projeto',
            'descricao' => 'descricao',
            'supervisor' => 'supervisor',
            'coordenador' => 'coordenador',
            'oc_tim' => 'oc_tim',
            'oc (tim)' => 'oc_tim',
            'chave_mw' => 'chave_mw',
            'chavemw' => 'chave_mw',
            'smp_nokia' => 'smp_nokia',
            'obs_geral' => 'observacao',
            'obs geral' => 'observacao',
            'data_cadastro_ativ' => 'data_abertura',
            'datacadastro' => 'data_abertura',
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
     * @param  array<string, int>  $estacoesPorSite
     * @param  array<string, int>  $clientesPorNome
     * @return array<string, mixed>|null
     */
    private function montarDados(array $valores, array $mapeamento, array $estacoesPorSite, array $clientesPorNome): ?array
    {
        $codigo = $this->campo($valores, $mapeamento, 'codigo');

        if ($codigo === null) {
            return null;
        }

        $estacaoASite = $this->campo($valores, $mapeamento, 'estacao_a');
        $estacaoBSite = $this->campo($valores, $mapeamento, 'estacao_b');

        $estacaoAId = $estacaoASite !== null ? ($estacoesPorSite[$estacaoASite] ?? null) : null;
        $estacaoBId = $estacaoBSite !== null ? ($estacoesPorSite[$estacaoBSite] ?? null) : null;

        $descricao = $this->campo($valores, $mapeamento, 'descricao');
        $projeto = $this->campo($valores, $mapeamento, 'projeto');

        $titulo = $this->montarTitulo($projeto, $descricao, $estacaoASite, $estacaoBSite);

        $escopo = $estacaoAId !== null ? 'Estação' : 'Outro';

        $clienteNome = $this->campo($valores, $mapeamento, 'cliente');

        return [
            'codigo' => $codigo,
            'codigo_personalizado' => $this->campo($valores, $mapeamento, 'codigo_personalizado'),
            'codigo_cliente' => $this->campo($valores, $mapeamento, 'codigo_cliente'),
            'cliente_id' => $clienteNome !== null ? ($clientesPorNome[$clienteNome] ?? null) : null,
            'ordem_complexa' => $this->campo($valores, $mapeamento, 'ordem_complexa'),
            'titulo' => $titulo,
            'tipo' => $this->montarTipo($projeto, $descricao),
            'escopo' => $escopo,
            'status' => $this->montarStatus($this->campo($valores, $mapeamento, 'status')),
            'prioridade' => 'Média',
            'estacao_a_id' => $estacaoAId,
            'estacao_b_id' => $estacaoBId,
            'descricao' => $descricao,
            'data_abertura' => $this->data($this->campo($valores, $mapeamento, 'data_abertura')),
            'projeto' => $projeto,
            'end_id_a' => $this->campo($valores, $mapeamento, 'end_id_a'),
            'end_id_b' => $this->campo($valores, $mapeamento, 'end_id_b'),
            'supervisor' => $this->campo($valores, $mapeamento, 'supervisor'),
            'coordenador' => $this->campo($valores, $mapeamento, 'coordenador'),
            'oc_tim' => $this->campo($valores, $mapeamento, 'oc_tim'),
            'chave_mw' => $this->campo($valores, $mapeamento, 'chave_mw'),
            'smp_nokia' => $this->campo($valores, $mapeamento, 'smp_nokia'),
            'observacao' => $this->campo($valores, $mapeamento, 'observacao'),
            'dados_brutos' => json_encode($valores),
        ];
    }

    private function montarTitulo(?string $projeto, ?string $descricao, ?string $siteA, ?string $siteB): string
    {
        $base = $descricao !== null ? trim(preg_replace('/\s+/', ' ', $descricao) ?? $descricao) : '';
        $base = mb_substr($base, 0, 120);

        if ($base !== '') {
            return $base;
        }

        $trecho = trim(($projeto ?? '').' '.($siteA ?? '').($siteB ? ' - '.$siteB : ''));

        return $trecho !== '' ? $trecho : 'Ordem de serviço';
    }

    private function montarTipo(?string $projeto, ?string $descricao): string
    {
        $texto = strtolower(trim(($projeto ?? '').' '.($descricao ?? '')));

        if (str_contains($texto, 'desinstala')) {
            return 'Remoção';
        }
        if (str_contains($texto, 'vistoria') || str_contains($texto, 'inspec')) {
            return 'Inspeção';
        }
        if (str_contains($texto, 'ativ')) {
            return 'Ativação';
        }
        if (str_contains($texto, 'instala') || str_contains($texto, 'intalacao') || str_contains($texto, 'reuso')) {
            return 'Instalação';
        }
        if (str_contains($texto, 'manuten')) {
            return 'Manutenção';
        }

        return 'Outro';
    }

    private function montarStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $texto = strtolower($status);

        if (str_contains($texto, 'pendente')) {
            return 'Aberta';
        }
        if (str_contains($texto, 'conclu')) {
            return 'Concluída';
        }
        if (str_contains($texto, 'cancel')) {
            return 'Cancelada';
        }
        if (str_contains($texto, 'aprov')) {
            return 'Em andamento';
        }

        return 'Aberta';
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

    private function data(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d', 'm/d/Y', 'd/m/Y H:i:s', 'Y-m-d H:i:s'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, trim($valor))->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
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
    private function gravarLote(array &$lote): void
    {
        if ($lote === []) {
            return;
        }

        OrdemServico::upsert(
            $lote,
            ['codigo'],
            ['codigo_personalizado', 'codigo_cliente', 'cliente_id', 'ordem_complexa',
                'titulo', 'tipo', 'status', 'prioridade', 'estacao_a_id', 'estacao_b_id',
                'descricao', 'data_abertura', 'projeto', 'end_id_a',
                'end_id_b', 'supervisor', 'coordenador', 'oc_tim', 'chave_mw',
                'smp_nokia', 'observacao', 'dados_brutos'],
        );

        OrdemServicoImport::where('id', $this->importId)->increment('importadas', count($lote));

        $lote = [];
    }
}
