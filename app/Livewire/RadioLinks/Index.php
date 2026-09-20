<?php

namespace App\Livewire\RadioLinks;

use App\Jobs\ProcessRadioLinkImport;
use App\Models\RadioLink;
use App\Models\RadioLinkImport;
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

#[Title('Radio Links')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = [
        'codigo', 'estacao_a_id', 'frequencia', 'capacidade', 'data_ativacao', 'status',
    ];

    public string $search = '';

    public string $filtroStatus = '';

    public string $filtroFabricante = '';

    public string $sortField = 'codigo';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $radioLinkParaExcluir = null;

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

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroFabricante(): void
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

    public function destroy(RadioLink $radioLink): void
    {
        $radioLink->delete();

        $this->radioLinkParaExcluir = null;
        $this->limparSelecao();

        $this->dispatch('radio-link-deleted');
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
        $idsPagina = $this->radioLinks()->pluck('id')->all();

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

        RadioLink::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('radio-link-deleted');
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
            'imports/radio-links',
            'local',
        );

        $import = RadioLinkImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => RadioLinkImport::STATUS_PENDENTE,
        ]);

        ProcessRadioLinkImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o iniciada. Os radio links serÃ£o importados em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = RadioLinkImport::query()
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

            if ($importacao->status === RadioLinkImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o concluÃ­da: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === RadioLinkImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === RadioLinkImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum radio link selecionado.'));
        }

        $radioLinks = RadioLink::with(['estacaoA', 'estacaoB'])
            ->whereIn('id', $this->selecionados)
            ->orderBy('codigo')
            ->get();

        return $exporter->download(
            'radio-links-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($radioLinks),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryRadioLinks()->with(['estacaoA', 'estacaoB']);

        return $exporter->download(
            'radio-links.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($query->orderBy('codigo')->get()),
        );
    }

    /**
     * @return array<int, string>
     */
    private function cabecalhoExportacao(): array
    {
        return [
            'CÃ³digo', 'Nome', 'EstaÃ§Ã£o A', 'EstaÃ§Ã£o B', 'FrequÃªncia',
            'Capacidade', 'Canal', 'PolarizaÃ§Ã£o', 'Fabricante', 'Modelo',
            'DistÃ¢ncia', 'Status', 'Data de ativaÃ§Ã£o',
        ];
    }

    /**
     * @param  Collection<int, RadioLink>  $radioLinks
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $radioLinks): array
    {
        return $radioLinks->map(function (RadioLink $radioLink): array {
            return [
                $radioLink->codigo,
                $radioLink->nome,
                $radioLink->estacaoA->site_id,
                $radioLink->estacaoB->site_id,
                $radioLink->frequencia !== null ? (float) $radioLink->frequencia : null,
                $radioLink->capacidade,
                $radioLink->canal,
                $radioLink->polarizacao,
                $radioLink->fabricante,
                $radioLink->modelo,
                $radioLink->distancia !== null ? (float) $radioLink->distancia : null,
                $radioLink->status,
                $radioLink->data_ativacao?->format('d/m/Y'),
            ];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return RadioLink::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function fabricantes(): array
    {
        return RadioLink::whereNotNull('fabricante')
            ->distinct()
            ->pluck('fabricante')
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
            'total' => RadioLink::query()->count(),
            'ativos' => RadioLink::query()->where('status', 'Ativo')->count(),
            'fabricantes' => RadioLink::query()->whereNotNull('fabricante')->distinct()->count(),
            'estacoes' => RadioLink::query()
                ->distinct()
                ->selectRaw('estacao_a_id')
                ->union(RadioLink::query()->distinct()->selectRaw('estacao_b_id'))
                ->count(),
        ];
    }

    #[Computed]
    public function radioLinkAlvo(): ?RadioLink
    {
        return $this->radioLinkParaExcluir
            ? RadioLink::find($this->radioLinkParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, RadioLink>
     */
    public function radioLinks(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryRadioLinks()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<RadioLink>
     */
    private function queryRadioLinks(): Builder
    {
        return RadioLink::query()
            ->with(['estacaoA', 'estacaoB'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%")
                        ->orWhere('fabricante', 'like', "%{$search}%")
                        ->orWhereHas('estacaoA', fn ($q) => $q->where('site_id', 'like', "%{$search}%"))
                        ->orWhereHas('estacaoB', fn ($q) => $q->where('site_id', 'like', "%{$search}%"));
                });
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('status', $this->filtroStatus);
            })
            ->when($this->filtroFabricante !== '', function ($query) {
                $query->where('fabricante', $this->filtroFabricante);
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroStatus', 'filtroFabricante']);

        $this->sortField = 'codigo';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.radio-links.index', [
            'radioLinks' => $this->radioLinks(),
        ]);
    }
}
