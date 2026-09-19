<?php

namespace App\Livewire\RadioLinks;

use App\Models\RadioLink;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Radio Links')]
class Index extends Component
{
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
