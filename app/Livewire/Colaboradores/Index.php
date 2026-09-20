<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Colaboradores')]
class Index extends Component
{
    use WithPagination;

    private const SORTABLE = [
        'nome', 'email', 'departamento', 'cargo', 'categoria',
        'data_admissao', 'salario', 'ativo',
    ];

    public string $search = '';

    public string $filtroDepartamento = '';

    public string $filtroCategoria = '';

    public string $filtroStatus = '';

    public string $sortField = 'nome';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $colaboradorParaExcluir = null;

    /** @var array<int, int> */
    public array $selecionados = [];

    /**
     * Update search and reset pagination.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroDepartamento(): void
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

    /**
     * Toggle the active status of a collaborator.
     */
    public function toggleAtivo(Colaborador $colaborador): void
    {
        $colaborador->update(['ativo' => ! $colaborador->ativo]);

        $this->dispatch('colaborador-updated');
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
        $idsPagina = $this->colaboradores()->pluck('id')->all();

        $todosSelecionados = array_diff($idsPagina, $this->selecionados) === [];

        $this->selecionados = $todosSelecionados
            ? array_values(array_diff($this->selecionados, $idsPagina))
            : array_values(array_unique(array_merge($this->selecionados, $idsPagina)));
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function ativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Colaborador::whereIn('id', $this->selecionados)->update(['ativo' => true]);

        $this->limparSelecao();
        $this->dispatch('colaborador-updated');
    }

    public function desativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Colaborador::whereIn('id', $this->selecionados)->update(['ativo' => false]);

        $this->limparSelecao();
        $this->dispatch('colaborador-updated');
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Colaborador::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('colaborador-deleted');
    }

    /**
     * Delete a collaborator.
     */
    public function destroy(Colaborador $colaborador): void
    {
        $colaborador->delete();

        $this->colaboradorParaExcluir = null;

        $this->dispatch('colaborador-deleted');
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function departamentos(): array
    {
        return Colaborador::whereNotNull('departamento')
            ->distinct()
            ->pluck('departamento')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categorias(): array
    {
        return Colaborador::CATEGORIAS;
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Colaborador::query()->count(),
            'ativos' => Colaborador::query()->where('ativo', true)->count(),
            'inativos' => Colaborador::query()->where('ativo', false)->count(),
            'departamentos' => Colaborador::query()->whereNotNull('departamento')->distinct()->count(),
        ];
    }

    #[Computed]
    public function colaboradorAlvo(): ?Colaborador
    {
        return $this->colaboradorParaExcluir
            ? Colaborador::find($this->colaboradorParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, Colaborador>
     */
    public function colaboradores(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Colaborador::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%")
                        ->orWhere('cargo', 'like', "%{$search}%")
                        ->orWhere('telefone', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroDepartamento, function ($query, $departamento) {
                $query->where('departamento', $departamento);
            })
            ->when($this->filtroCategoria, function ($query, $categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            })
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * Clear all active search and filter criteria.
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroDepartamento', 'filtroCategoria', 'filtroStatus']);

        $this->sortField = 'nome';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.colaboradores.index', [
            'colaboradores' => $this->colaboradores(),
        ]);
    }
}
