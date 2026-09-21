<?php

namespace App\Livewire\Clientes;

use App\Models\ClienteSegmento;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Configurações de Clientes')]
class Configuracoes extends Component
{
    public bool $showModal = false;

    public ?int $itemId = null;

    public string $nome = '';

    public string $descricao = '';

    public bool $ativo = true;

    public function abrirNovo(): void
    {
        $this->reset(['itemId', 'nome', 'descricao']);
        $this->ativo = true;
        $this->showModal = true;
    }

    public function abrirEdicao(int $itemId): void
    {
        $item = $this->modelo()->findOrFail($itemId);

        $this->itemId = $item->id;
        $this->nome = $item->nome;
        $this->descricao = $item->descricao ?? '';
        $this->ativo = $item->ativo;
        $this->showModal = true;
    }

    public function fecharModal(): void
    {
        $this->showModal = false;
        $this->reset(['itemId', 'nome', 'descricao']);
    }

    public function salvar(): void
    {
        $tabela = $this->modelo()->getTable();

        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255', "unique:{$tabela},nome,{$this->itemId}"],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativo' => ['boolean'],
        ]);

        $validated['descricao'] = $validated['descricao'] === '' ? null : $validated['descricao'];

        if ($this->itemId) {
            $this->modelo()->findOrFail($this->itemId)->update($validated);

            Flux::toast(variant: 'success', text: __('Segmento atualizado com sucesso.'));
        } else {
            $this->modelo()->create($validated);

            Flux::toast(variant: 'success', text: __('Segmento criado com sucesso.'));
        }

        $this->fecharModal();
    }

    public function toggleAtivo(int $itemId): void
    {
        $item = $this->modelo()->findOrFail($itemId);
        $item->update(['ativo' => ! $item->ativo]);

        $this->dispatch('cliente-configuracao-updated');
    }

    public function destroy(int $itemId): void
    {
        $this->modelo()->findOrFail($itemId)->delete();

        Flux::toast(text: __('Segmento removido.'));

        $this->dispatch('cliente-configuracao-deleted');
    }

    /**
     * @return Collection<int, Model>
     */
    #[Computed]
    public function itens(): Collection
    {
        return $this->modelo()->orderBy('nome')->get();
    }

    private function modelo(): mixed
    {
        return new ClienteSegmento;
    }

    public function render(): View
    {
        return view('livewire.clientes.configuracoes');
    }
}
