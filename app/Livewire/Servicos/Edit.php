<?php

namespace App\Livewire\Servicos;

use App\Models\Servico;
use App\Models\ServicoCategoria;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Serviço')]
class Edit extends Component
{
    public ?Servico $servico = null;

    public string $nome = '';

    public string $codigo = '';

    public string $categoria = '';

    public string $descricao = '';

    public ?string $preco = null;

    public string $observacoes = '';

    public bool $ativo = true;

    public function mount(Servico $servico): void
    {
        $this->servico = $servico;
        $this->nome = $servico->nome;
        $this->codigo = $servico->codigo ?? '';
        $this->categoria = $servico->categoria ?? '';
        $this->descricao = $servico->descricao ?? '';
        $this->preco = $servico->preco ? (string) $servico->preco : '';
        $this->observacoes = $servico->observacoes ?? '';
        $this->ativo = $servico->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:servicos,codigo,'.$this->servico->id],
            'categoria' => ['required', Rule::in($this->categorias())],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $this->servico->update($validated);

        Flux::toast(variant: 'success', text: __('Serviço atualizado com sucesso.'));

        $this->redirect(route('servicos.index'), navigate: true);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categorias(): array
    {
        $categorias = ServicoCategoria::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return $categorias !== [] ? $categorias : Servico::CATEGORIAS;
    }

    public function render(): View
    {
        return view('livewire.servicos.edit', [
            'categorias' => $this->categorias(),
        ]);
    }
}
