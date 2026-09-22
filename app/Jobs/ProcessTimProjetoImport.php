<?php

namespace App\Jobs;

use App\Models\Cliente;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Comment\TextRun;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\CSV\Options;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ProcessTimProjetoImport implements ShouldQueue
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
        $import = TimProjetoImport::findOrFail($this->importId);

        if ($import->status === TimProjetoImport::STATUS_PROCESSANDO) {
            return;
        }

        $import->update([
            'status' => TimProjetoImport::STATUS_PROCESSANDO,
            'processadas' => 0,
            'importadas' => 0,
            'ignoradas' => 0,
            'erro' => null,
        ]);

        $caminho = Storage::disk('local')->path($import->arquivo);

        if (! is_file($caminho)) {
            $import->update([
                'status' => TimProjetoImport::STATUS_FALHOU,
                'erro' => __('O arquivo da importação não foi encontrado.'),
            ]);

            return;
        }

        $clientesPorNome = Cliente::pluck('id', 'nome')->all();

        $estacoesPorSite = Estacao::pluck('id', 'site_id')->all();

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

                    $dados = $this->montarDados($valores, $mapeamento, $clientesPorNome, $estacoesPorSite);

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
                'status' => TimProjetoImport::STATUS_CONCLUIDO,
                'total_linhas' => max(0, $totalLinhas - 1),
                'processadas' => $processadas,
                'ignoradas' => $ignoradas,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => TimProjetoImport::STATUS_FALHOU,
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
            'codigo_personalizado' => 'codigo',
            'cod personalizado' => 'codigo',
            'nome' => 'codigo',
            'projeto' => 'codigo',
            'descricao' => 'descricao',
            'status' => 'status',
            'oc' => 'oc',
            'os_fam_entrega' => 'os_fam_entrega',
            'os fam entrega' => 'os_fam_entrega',
            'os_fam_instalacao' => 'os_fam_instalacao',
            'os fam instalacao' => 'os_fam_instalacao',
            'os_fam_panoramica' => 'os_fam_panoramica',
            'os fam panoramica' => 'os_fam_panoramica',
            'os_fam_desinstalacao' => 'os_fam_desinstalacao',
            'os fam desinstalacao' => 'os_fam_desinstalacao',
            'cliente' => 'cliente',
            'nome_cliente' => 'cliente',
            'estacao' => 'estacao',
            'site_id' => 'estacao',
            'site' => 'estacao',
            'data_inicio' => 'data_inicio',
            'data inicio' => 'data_inicio',
            'inicio' => 'data_inicio',
            'data_fim' => 'data_fim',
            'data fim' => 'data_fim',
            'fim' => 'data_fim',
            'ativo' => 'ativo',
            'situacao' => 'ativo',
            'criar_os' => 'criar_os',
            'criar ordem' => 'criar_os',
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
     * @param  array<string, int>  $clientesPorNome
     * @param  array<string, int>  $estacoesPorSite
     * @return array<string, mixed>|null
     */
    private function montarDados(array $valores, array $mapeamento, array $clientesPorNome, array $estacoesPorSite): ?array
    {
        $codigo = $this->campo($valores, $mapeamento, 'codigo');

        if ($codigo === null) {
            return null;
        }

        $clienteNome = $this->campo($valores, $mapeamento, 'cliente');

        $estacaoSite = $this->campo($valores, $mapeamento, 'estacao');

        $status = $this->campo($valores, $mapeamento, 'status');
        $status = $status !== null && in_array($status, TimProjeto::STATUS, true) ? $status : 'Planejamento';

        $ativo = $this->campo($valores, $mapeamento, 'ativo');

        return [
            'codigo' => $codigo,
            'nome' => $codigo,
            'descricao' => $this->campo($valores, $mapeamento, 'descricao'),
            'status' => $status,
            'oc' => $this->campo($valores, $mapeamento, 'oc'),
            'os_fam_entrega' => $this->campo($valores, $mapeamento, 'os_fam_entrega'),
            'os_fam_instalacao' => $this->campo($valores, $mapeamento, 'os_fam_instalacao'),
            'os_fam_panoramica' => $this->campo($valores, $mapeamento, 'os_fam_panoramica'),
            'os_fam_desinstalacao' => $this->campo($valores, $mapeamento, 'os_fam_desinstalacao'),
            'cliente_id' => $clienteNome !== null ? ($clientesPorNome[$clienteNome] ?? null) : null,
            'estacao_id' => $estacaoSite !== null ? ($estacoesPorSite[$estacaoSite] ?? null) : null,
            'data_inicio' => $this->data($this->campo($valores, $mapeamento, 'data_inicio')),
            'data_fim' => $this->data($this->campo($valores, $mapeamento, 'data_fim')),
            'ativo' => $ativo === null || $this->ehAtivo($ativo),
            'criar_os' => $this->ehVerdadeiro($this->campo($valores, $mapeamento, 'criar_os')),
        ];
    }

    private function ehAtivo(?string $valor): bool
    {
        $texto = mb_strtolower(trim((string) $valor));

        return ! in_array($texto, ['0', 'não', 'nao', 'false', 'inativo', 'no', 'n'], true);
    }

    private function ehVerdadeiro(?string $valor): bool
    {
        if ($valor === null) {
            return false;
        }

        $texto = mb_strtolower(trim($valor));

        return in_array($texto, ['1', 'sim', 's', 'true', 'yes', 'y'], true);
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

        $projetosParaCriar = [];

        foreach ($lote as $dados) {
            $criarOs = (bool) ($dados['criar_os'] ?? false);
            unset($dados['criar_os']);

            $estacaoId = $dados['estacao_id'] ?? null;
            unset($dados['estacao_id']);

            $projetosParaCriar[] = [
                'dados' => $dados,
                'estacao_id' => $estacaoId,
                'criar_os' => $criarOs,
            ];
        }

        $registros = array_map(fn (array $item): array => $item['dados'], $projetosParaCriar);

        TimProjeto::upsert(
            $registros,
            ['codigo'],
            ['nome', 'descricao', 'status', 'oc', 'os_fam_entrega', 'os_fam_instalacao',
                'os_fam_panoramica', 'os_fam_desinstalacao', 'cliente_id',
                'data_inicio', 'data_fim', 'ativo', 'updated_at'],
        );

        foreach ($projetosParaCriar as $item) {
            $projeto = TimProjeto::where('codigo', $item['dados']['codigo'])->first();

            if (! $projeto) {
                continue;
            }

            $projeto->ensureEtapas();

            if ($item['estacao_id'] !== null) {
                Estacao::whereKey($item['estacao_id'])
                    ->whereNull('projeto_tim_id')
                    ->update(['projeto_tim_id' => $projeto->id]);
            }

            if ($item['criar_os'] && $projeto->ordensServico()->doesntExist()) {
                OrdemServico::create([
                    'codigo' => $this->gerarCodigoOrdem(),
                    'titulo' => __('Serviço do projeto').' '.$projeto->codigo,
                    'tipo' => 'Instalação',
                    'escopo' => 'Estação',
                    'status' => 'Aberta',
                    'prioridade' => 'Média',
                    'estacao_a_id' => $item['estacao_id'],
                    'data_abertura' => now()->toDateString(),
                    'projeto_tim_id' => $projeto->id,
                ]);
            }
        }

        TimProjetoImport::where('id', $this->importId)->increment('importadas', count($lote));

        $lote = [];
    }

    private function gerarCodigoOrdem(): string
    {
        $ultimo = OrdemServico::query()
            ->where('codigo', 'like', 'OS-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'OS-'))
            ->max() ?? 0;

        return 'OS-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }
}