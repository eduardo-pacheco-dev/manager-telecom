<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Colaborador')]
class Show extends Component
{
    public ?Colaborador $colaborador = null;

    public bool $showDeleteModal = false;

    public function mount(Colaborador $colaborador): void
    {
        $this->colaborador = $colaborador;
    }

    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    public function destroy(): void
    {
        $this->colaborador->delete();

        $this->redirect(route('colaboradores.index'), navigate: true);
    }

    public function toggleAtivo(): void
    {
        $this->colaborador->update(['ativo' => ! $this->colaborador->ativo]);

        $this->colaborador->refresh();
    }

    public function render(): View
    {
        return view('livewire.colaboradores.show');
    }
}
