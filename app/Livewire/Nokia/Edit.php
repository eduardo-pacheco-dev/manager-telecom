<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Projeto Nokia')]
class Edit extends Component
{
    public ?NokiaProjeto $projeto = null;

    public string $codigo = '';

    public string $nome = '';

    public string $descricao = '';

    public string $status = '';

    public ?string $data_inicio = null;

    public ?string $data_fim = null;

    public bool $ativo = true;

    public function mount(NokiaProjeto $projeto): void
    {
        $this->projeto = $projeto;
        $this->codigo = $projeto->codigo;
        $this->nome = $projeto->nome;
        $this->descricao = $projeto->descricao ?? '';
        $this->status = $projeto->status;
        $this->data_inicio = $projeto->data_inicio?->format('Y-m-d');
        $this->data_fim = $projeto->data_fim?->format('Y-m-d');
        $this->ativo = $projeto->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:nokia_projetos,codigo,'.$this->projeto->id],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(NokiaProjeto::STATUS)],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date'],
            'ativo' => ['boolean'],
        ]);

        $this->projeto->update($validated);

        Flux::toast(variant: 'success', text: __('Projeto Nokia atualizado com sucesso.'));

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.nokia.edit');
    }
}
