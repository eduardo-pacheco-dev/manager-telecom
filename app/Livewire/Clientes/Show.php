<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Cliente')]
class Show extends Component
{
    public ?Cliente $cliente = null;

    public bool $showDeleteModal = false;

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
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
        $this->cliente->delete();

        $this->redirect(route('clientes.index'), navigate: true);
    }

    public function toggleAtivo(): void
    {
        $this->cliente->update(['ativo' => ! $this->cliente->ativo]);

        $this->cliente->refresh();
    }

    public function render(): View
    {
        return view('livewire.clientes.show');
    }
}
