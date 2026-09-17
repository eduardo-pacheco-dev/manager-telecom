<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Colaboradores')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroDepartamento = '';

    public string $filtroStatus = '';

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

    public function updatingFiltroStatus(): void
    {
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

    /**
     * Delete a collaborator.
     */
    public function destroy(Colaborador $colaborador): void
    {
        $colaborador->delete();

        $this->dispatch('colaborador-deleted');
    }

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

    public function colaboradores()
    {
        return Colaborador::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroDepartamento, function ($query, $departamento) {
                $query->where('departamento', $departamento);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            })
            ->orderBy('nome')
            ->paginate(10);
    }

    /**
     * Clear all active search and filter criteria.
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroDepartamento', 'filtroStatus']);

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.colaboradores.index', [
            'colaboradores' => $this->colaboradores(),
        ]);
    }
}
