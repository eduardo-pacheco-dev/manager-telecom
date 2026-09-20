<?php

namespace App\Livewire\Estacoes;

use App\Jobs\ProcessEstacaoImport;
use App\Models\Estacao;
use App\Models\EstacaoImport;
use App\Services\ExcelExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('EstaÃ§Ãµes')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = [
        'site_id', 'tipo_elemento', 'classificacao', 'tecnologia',
        'municipio', 'data_aquisicao', 'status',
    ];

    public string $search = '';

    public string $filtroTipoElemento = '';

    public string $filtroStatus = '';

    public string $sortField = 'site_id';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $estacaoParaExcluir = null;

    public bool $showImportModal = false;

    public ?TemporaryUploadedFile $import_arquivo = null;

    /** @var array<int, string> */
    public array $importesStatus = [];

    /** @var array<int, int> */
    public array $selecionados = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipoElemento(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $campo): void
    {
        if (! in_array($campo, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $campo) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $campo;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function destroy(Estacao $estacao): void
    {
        $estacao->delete();

        $this->estacaoParaExcluir = null;
        $this->limparSelecao();

        $this->dispatch('estacao-deleted');
    }

    public function alternarSelecao(int $id): void
    {
        if (in_array($id, $this->selecionados, true)) {
            $this->selecionados = array_values(array_diff($this->selecionados, [$id]));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodosDaPagina(): void
    {
        $idsPagina = $this->estacoes()->pluck('id')->all();

        $todosSelecionados = array_diff($idsPagina, $this->selecionados) === [];

        $this->selecionados = $todosSelecionados
            ? array_values(array_diff($this->selecionados, $idsPagina))
            : array_values(array_unique(array_merge($this->selecionados, $idsPagina)));
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Estacao::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('estacao-deleted');
    }

    public function abrirImportacao(): void
    {
        $this->showImportModal = true;
    }

    public function fecharImportacao(): void
    {
        $this->showImportModal = false;
        $this->reset('import_arquivo');
    }

    public function limparArquivoImportacao(): void
    {
        $this->reset('import_arquivo');
    }

    public function iniciarImportacao(): void
    {
        $this->validate([
            'import_arquivo' => ['required', 'file', 'max:204800', 'mimes:xlsx,csv'],
        ], [
            'import_arquivo.required' => __('Escolha um arquivo Excel para importar.'),
            'import_arquivo.file' => __('O valor deve ser um arquivo.'),
            'import_arquivo.max' => __('O arquivo nÃ£o pode ter mais de 200 MB.'),
            'import_arquivo.mimes' => __('O arquivo deve ser um Excel (.xlsx) ou CSV.'),
        ]);

        $caminho = $this->import_arquivo->store(
            'imports/estacao',
            'local',
        );

        $import = EstacaoImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => EstacaoImport::STATUS_PENDENTE,
        ]);

        ProcessEstacaoImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o iniciada. As estaÃ§Ãµes serÃ£o importadas em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = EstacaoImport::query()
            ->latest()
            ->limit(5)
            ->get();

        foreach ($importacoes as $importacao) {
            $statusAnterior = $this->importesStatus[$importacao->id] ?? null;

            if ($statusAnterior === $importacao->status) {
                continue;
            }

            if ($statusAnterior === null) {
                $this->importesStatus[$importacao->id] = $importacao->status;

                continue;
            }

            $this->importesStatus[$importacao->id] = $importacao->status;

            if ($importacao->status === EstacaoImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o concluÃ­da: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === EstacaoImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === EstacaoImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhuma estaÃ§Ã£o selecionada.'));
        }

        $estacoes = Estacao::whereIn('id', $this->selecionados)
            ->orderBy('site_id')
            ->get();

        return $exporter->download(
            'estacoes-selecionadas.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($estacoes),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryEstacoes();

        return $exporter->download(
            'estacoes.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($query->orderBy('site_id')->get()),
        );
    }

    /**
     * @return array<int, string>
     */
    private function cabecalhoExportacao(): array
    {
        return [
            'Site ID', 'EndereÃ§o ID', 'Tipo de elemento', 'Tecnologia', 'ClassificaÃ§Ã£o',
            'MunicÃ­pio', 'Estado', 'Regional', 'Status', 'Data de aquisiÃ§Ã£o',
        ];
    }

    /**
     * @param  Collection<int, Estacao>  $estacoes
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $estacoes): array
    {
        return $estacoes->map(function (Estacao $estacao): array {
            return [
                $estacao->site_id,
                $estacao->endereco_id,
                $estacao->tipo_elemento,
                $estacao->tecnologia,
                $estacao->classificacao,
                $estacao->municipio,
                $estacao->estado,
                $estacao->regional,
                $estacao->status,
                $estacao->data_aquisicao?->format('d/m/Y'),
            ];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tiposElemento(): array
    {
        return Estacao::whereNotNull('tipo_elemento')
            ->distinct()
            ->pluck('tipo_elemento')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return Estacao::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Estacao::query()->count(),
            'tecnologias' => Estacao::query()->whereNotNull('tecnologia')->distinct()->count(),
            'municipios' => Estacao::query()->whereNotNull('municipio')->distinct()->count(),
            'elementos' => Estacao::query()->whereNotNull('tipo_elemento')->distinct()->count(),
        ];
    }

    #[Computed]
    public function estacaoAlvo(): ?Estacao
    {
        return $this->estacaoParaExcluir
            ? Estacao::find($this->estacaoParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, Estacao>
     */
    public function estacoes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryEstacoes()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<Estacao>
     */
    private function queryEstacoes(): Builder
    {
        return Estacao::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('site_id', 'like', "%{$search}%")
                        ->orWhere('tipo_elemento', 'like', "%{$search}%")
                        ->orWhere('municipio', 'like', "%{$search}%")
                        ->orWhere('endereco_id', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroTipoElemento, function ($query, $tipoElemento) {
                $query->where('tipo_elemento', $tipoElemento);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('status', $this->filtroStatus);
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroTipoElemento', 'filtroStatus']);

        $this->sortField = 'site_id';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.estacoes.index', [
            'estacoes' => $this->estacoes(),
        ]);
    }
}
