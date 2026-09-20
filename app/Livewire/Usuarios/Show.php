<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Usuário')]
class Show extends Component
{
    public ?User $usuario = null;

    public bool $showDeleteModal = false;

    public function mount(User $usuario): void
    {
        $this->usuario = $usuario;
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
        if ($this->usuario->id === auth()->id()) {
            $this->dispatch('flux-toast', text: __('Você não pode excluir o próprio usuário.'), variant: 'danger');

            return;
        }

        $this->usuario->delete();

        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.usuarios.show');
    }
}
