<?php

namespace App\Livewire\Servicos;

use App\Models\Servico;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Serviço')]
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
            'codigo' => ['nullable', 'string', 'max:50', 'unique:servicos,codigo'],
            'categoria' => ['required', Rule::in(Servico::CATEGORIAS)],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        Servico::create($validated);

        Flux::toast(variant: 'success', text: __('Serviço criado com sucesso.'));

        $this->redirect(route('servicos.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.servicos.create', [
            'categorias' => Servico::CATEGORIAS,
        ]);
    }
}
