<?php

namespace App\Livewire\RadioLinks;

use App\Jobs\ProcessRadioLinkImport;
use App\Models\RadioLink;
use App\Models\RadioLinkImport;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

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

    public bool $showImportModal = false;

    public $import_arquivo = null;

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
            'import_arquivo.max' => __('O arquivo não pode ter mais de 200 MB.'),
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

        $this->dispatch('flux-toast', text: __('Importação iniciada. Os radio links serão importados em segundo plano.'), variant: 'success');
    }

    /**
     * @return Collection<int, RadioLinkImport>
     */
    #[Computed]
    public function importacoes(): Collection
    {
        return RadioLinkImport::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
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

    /**
     * @return LengthAwarePaginator<int, RadioLink>
     */
    public function radioLinks(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
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
            })
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
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
