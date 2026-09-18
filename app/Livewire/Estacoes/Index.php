<?php

namespace App\Livewire\Estacoes;

use App\Models\Estacao;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Estações')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroTipoElemento = '';

    public string $filtroStatus = '';

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

    public function destroy(Estacao $estacao): void
    {
        $estacao->delete();

        $this->dispatch('estacao-deleted');
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

    /**
     * @return LengthAwarePaginator<int, Estacao>
     */
    public function estacoes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
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
            })
            ->orderBy('site_id')
            ->paginate(10);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroTipoElemento', 'filtroStatus']);

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.estacoes.index', [
            'estacoes' => $this->estacoes(),
        ]);
    }
}
