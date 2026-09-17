<?php

namespace App\Livewire\Produtos;

use App\Models\Produto;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Produto')]
class Create extends Component
{
    public string $nome = '';

    public string $codigo = '';

    public string $categoria = '';

    public string $descricao = '';

    public ?string $preco = null;

    public string $observacoes = '';

    public bool $ativo = true;

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:produtos,codigo'],
            'categoria' => ['required', Rule::in(Produto::CATEGORIAS)],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        Produto::create($validated);

        Flux::toast(variant: 'success', text: __('Produto criado com sucesso.'));

        $this->redirect(route('produtos.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.produtos.create', [
            'categorias' => Produto::CATEGORIAS,
        ]);
    }
}
