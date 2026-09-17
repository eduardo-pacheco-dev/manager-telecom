<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Produto')]
class Edit extends Component
{
    public ?Produto $produto = null;

    public string $nome = '';

    public string $codigo = '';

    public string $categoria = '';

    public string $descricao = '';

    public ?string $preco = null;

    public string $observacoes = '';

    public bool $ativo = true;

    public function mount(Produto $produto): void
    {
        $this->produto = $produto;
        $this->nome = $produto->nome;
        $this->codigo = $produto->codigo ?? '';
        $this->categoria = $produto->categoria ?? '';
        $this->descricao = $produto->descricao ?? '';
        $this->preco = $produto->preco ? (string) $produto->preco : '';
        $this->observacoes = $produto->observacoes ?? '';
        $this->ativo = $produto->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:produtos,codigo,'.$this->produto->id],
            'categoria' => ['required', Rule::in(Produto::CATEGORIAS)],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $this->produto->update($validated);

        Flux::toast(variant: 'success', text: __('Produto atualizado com sucesso.'));

        $this->redirect(route('produtos.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.produtos.edit', [
            'categorias' => Produto::CATEGORIAS,
        ]);
    }
}
