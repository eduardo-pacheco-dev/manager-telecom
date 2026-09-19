<?php

namespace App\Livewire\OrdensServico;

use App\Models\OrdemServico;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Ordens de Serviço')]
class Index extends Component
{
    use WithPagination;

    private const SORTABLE = [
        'codigo', 'titulo', 'tipo', 'status', 'prioridade', 'radio_link_id', 'data_abertura',
    ];

    public string $search = '';

    public string $filtroStatus = '';

    public string $filtroTipo = '';

    public string $filtroPrioridade = '';

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

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroPrioridade(): void
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

    public function destroy(OrdemServico $ordemServico): void
    {
        $ordemServico->delete();

        $this->dispatch('ordem-servico-deleted');
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return OrdemServico::whereNotNull('status')
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
    public function tipos(): array
    {
        return OrdemServico::whereNotNull('tipo')
            ->distinct()
            ->pluck('tipo')
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
            'total' => OrdemServico::query()->count(),
            'abertas' => OrdemServico::query()->whereIn('status', ['Aberta', 'Em andamento', 'Aguardando'])->count(),
            'concluidas' => OrdemServico::query()->where('status', 'Concluída')->count(),
            'urgentes' => OrdemServico::query()->where('prioridade', 'Urgente')->where('status', '!=', 'Concluída')->count(),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, OrdemServico>
     */
    public function ordensServico(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return OrdemServico::query()
            ->with(['radioLink', 'responsavel'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('titulo', 'like', "%{$search}%")
                        ->orWhere('solicitante', 'like', "%{$search}%")
                        ->orWhereHas('radioLink', fn ($q) => $q->where('codigo', 'like', "%{$search}%"));
                });
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('status', $this->filtroStatus);
            })
            ->when($this->filtroTipo !== '', function ($query) {
                $query->where('tipo', $this->filtroTipo);
            })
            ->when($this->filtroPrioridade !== '', function ($query) {
                $query->where('prioridade', $this->filtroPrioridade);
            })
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroStatus', 'filtroTipo', 'filtroPrioridade']);

        $this->sortField = 'codigo';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.ordens-servico.index', [
            'ordensServico' => $this->ordensServico(),
        ]);
    }
}
