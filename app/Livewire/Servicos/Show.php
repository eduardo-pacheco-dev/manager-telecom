<?php

namespace App\Livewire\Servicos;

use App\Models\Servico;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Serviço')]
class Show extends Component
{
    public ?Servico $servico = null;

    public bool $showDeleteModal = false;

    public function mount(Servico $servico): void
    {
        $this->servico = $servico;
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
        $this->servico->delete();

        $this->redirect(route('servicos.index'), navigate: true);
    }

    public function toggleAtivo(): void
    {
        $this->servico->update(['ativo' => ! $this->servico->ativo]);

        $this->servico->refresh();
    }

    public function render()
    {
        return view('livewire.servicos.show');
    }
}
