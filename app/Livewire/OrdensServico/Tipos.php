<?php

namespace App\Livewire\OrdensServico;

use App\Models\OrdemServicoTipo;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tipos de Ordem de Serviço')]
class Tipos extends Component
{
    public bool $showModal = false;

    public ?int $tipoId = null;

    public string $nome = '';

    public string $descricao = '';

    public bool $ativo = true;

    public function abrirNovo(): void
    {
        $this->reset(['tipoId', 'nome', 'descricao']);
        $this->ativo = true;
        $this->showModal = true;
    }

    public function abrirEdicao(OrdemServicoTipo $tipo): void
    {
        $this->tipoId = $tipo->id;
        $this->nome = $tipo->nome;
        $this->descricao = $tipo->descricao ?? '';
        $this->ativo = $tipo->ativo;
        $this->showModal = true;
    }

    public function fecharModal(): void
    {
        $this->showModal = false;
        $this->reset(['tipoId', 'nome', 'descricao']);
    }

    public function salvar(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:ordem_servico_tipos,nome,'.$this->tipoId],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativo' => ['boolean'],
        ]);

        $validated['descricao'] = $validated['descricao'] === '' ? null : $validated['descricao'];

        if ($this->tipoId) {
            OrdemServicoTipo::findOrFail($this->tipoId)->update($validated);

            Flux::toast(variant: 'success', text: __('Tipo atualizado com sucesso.'));
        } else {
            OrdemServicoTipo::create($validated);

            Flux::toast(variant: 'success', text: __('Tipo criado com sucesso.'));
        }

        $this->fecharModal();
    }

    public function toggleAtivo(OrdemServicoTipo $tipo): void
    {
        $tipo->update(['ativo' => ! $tipo->ativo]);

        $this->dispatch('ordem-servico-tipo-updated');
    }

    public function destroy(OrdemServicoTipo $tipo): void
    {
        $tipo->delete();

        Flux::toast(text: __('Tipo removido.'));

        $this->dispatch('ordem-servico-tipo-deleted');
    }

    /**
     * @return Collection<int, OrdemServicoTipo>
     */
    #[Computed]
    public function tipos(): Collection
    {
        return OrdemServicoTipo::query()
            ->orderBy('nome')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.ordens-servico.tipos');
    }
}
