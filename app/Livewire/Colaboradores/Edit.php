<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Colaborador')]
class Edit extends Component
{
    public ?Colaborador $colaborador = null;

    public string $nome = '';

    public string $email = '';

    public string $cpf = '';

    public string $telefone = '';

    public string $cargo = '';

    public string $departamento = '';

    public string $categoria = '';

    public ?string $data_admissao = null;

    public ?string $salario = null;

    public string $endereco = '';

    public string $cidade = '';

    public string $estado = '';

    public string $cep = '';

    public string $observacoes = '';

    public bool $ativo = true;

    public function mount(Colaborador $colaborador): void
    {
        $this->colaborador = $colaborador;
        $this->nome = $colaborador->nome;
        $this->email = $colaborador->email;
        $this->cpf = $colaborador->cpf;
        $this->telefone = $colaborador->telefone ?? '';
        $this->cargo = $colaborador->cargo ?? '';
        $this->departamento = $colaborador->departamento ?? '';
        $this->categoria = $colaborador->categoria ?? '';
        $this->data_admissao = $colaborador->data_admissao?->format('Y-m-d');
        $this->salario = $colaborador->salario ? (string) $colaborador->salario : '';
        $this->endereco = $colaborador->endereco ?? '';
        $this->cidade = $colaborador->cidade ?? '';
        $this->estado = $colaborador->estado ?? '';
        $this->cep = $colaborador->cep ?? '';
        $this->observacoes = $colaborador->observacoes ?? '';
        $this->ativo = $colaborador->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:colaboradores,email,'.$this->colaborador->id],
            'cpf' => ['required', 'string', 'max:14', 'unique:colaboradores,cpf,'.$this->colaborador->id],
            'telefone' => ['nullable', 'string', 'max:20'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'departamento' => ['nullable', 'string', 'max:255'],
            'categoria' => ['required', Rule::in(Colaborador::CATEGORIAS)],
            'data_admissao' => ['nullable', 'date'],
            'salario' => ['nullable', 'numeric', 'min:0'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:10'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $this->colaborador->update($validated);

        Flux::toast(variant: 'success', text: __('Colaborador atualizado com sucesso.'));

        $this->redirect(route('colaboradores.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.colaboradores.edit', [
            'categorias' => Colaborador::CATEGORIAS,
        ]);
    }
}
