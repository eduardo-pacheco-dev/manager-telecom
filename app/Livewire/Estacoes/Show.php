<?php

namespace App\Livewire\Estacoes;

use App\Models\Estacao;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes da Estação')]
class Show extends Component
{
    public ?Estacao $estacao = null;

    public bool $showDeleteModal = false;

    public function mount(Estacao $estacao): void
    {
        $this->estacao = $estacao;
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
        $this->estacao->delete();

        $this->redirect(route('estacoes.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.estacoes.show');
    }
}
