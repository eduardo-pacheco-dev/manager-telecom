<?php

namespace App\Livewire\Servicos;

use App\Models\Servico;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Serviços')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroCategoria = '';

    public string $filtroStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function toggleAtivo(Servico $servico): void
    {
        $servico->update(['ativo' => ! $servico->ativo]);

        $this->dispatch('servico-updated');
    }

    public function destroy(Servico $servico): void
    {
        $servico->delete();

        $this->dispatch('servico-deleted');
    }

    #[Computed]
    public function categorias(): array
    {
        return Servico::whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values()
            ->all();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Servico::query()->count(),
            'ativos' => Servico::query()->where('ativo', true)->count(),
            'inativos' => Servico::query()->where('ativo', false)->count(),
            'categorias' => Servico::query()->whereNotNull('categoria')->distinct()->count(),
        ];
    }

    public function servicos()
    {
        return Servico::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('codigo', 'like', "%{$search}%")
                        ->orWhere('categoria', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroCategoria, function ($query, $categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            })
            ->orderBy('nome')
            ->paginate(10);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroCategoria', 'filtroStatus']);

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.servicos.index', [
            'servicos' => $this->servicos(),
        ]);
    }
}
