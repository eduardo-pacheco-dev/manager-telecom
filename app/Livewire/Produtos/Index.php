<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Produtos')]
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

    public function toggleAtivo(Produto $produto): void
    {
        $produto->update(['ativo' => ! $produto->ativo]);

        $this->dispatch('produto-updated');
    }

    public function destroy(Produto $produto): void
    {
        $produto->delete();

        $this->dispatch('produto-deleted');
    }

    #[Computed]
    public function categorias(): array
    {
        return Produto::whereNotNull('categoria')
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
            'total' => Produto::query()->count(),
            'ativos' => Produto::query()->where('ativo', true)->count(),
            'inativos' => Produto::query()->where('ativo', false)->count(),
            'categorias' => Produto::query()->whereNotNull('categoria')->distinct()->count(),
        ];
    }

    public function produtos()
    {
        return Produto::query()
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
        return view('livewire.produtos.index', [
            'produtos' => $this->produtos(),
        ]);
    }
}
