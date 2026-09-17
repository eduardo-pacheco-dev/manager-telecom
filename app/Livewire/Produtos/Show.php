<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Produto')]
class Show extends Component
{
    public ?Produto $produto = null;

    public bool $showDeleteModal = false;

    public function mount(Produto $produto): void
    {
        $this->produto = $produto;
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
        $this->produto->delete();

        $this->redirect(route('produtos.index'), navigate: true);
    }

    public function toggleAtivo(): void
    {
        $this->produto->update(['ativo' => ! $this->produto->ativo]);

        $this->produto->refresh();
    }

    public function render(): View
    {
        return view('livewire.produtos.show');
    }
}
