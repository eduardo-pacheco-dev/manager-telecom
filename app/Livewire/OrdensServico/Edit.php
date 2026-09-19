<?php

namespace App\Livewire\OrdensServico;

use App\Models\OrdemServico;
use App\Models\RadioLink;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Ordem de Serviço')]
class Edit extends Component
{
    public ?OrdemServico $ordemServico = null;

    public string $codigo = '';

    public string $titulo = '';

    public string $tipo = '';

    public string $status = '';

    public string $prioridade = '';

    public ?string $radio_link_id = null;

    public ?string $estacao_a_id = null;

    public ?string $estacao_b_id = null;

    public string $solicitante = '';

    public ?string $responsavel_id = null;

    public string $descricao = '';

    public ?string $data_abertura = null;

    public ?string $data_agendamento = null;

    public ?string $data_conclusao = null;

    public function mount(OrdemServico $ordemServico): void
    {
        $this->ordemServico = $ordemServico;
        $this->codigo = $ordemServico->codigo;
        $this->titulo = $ordemServico->titulo;
        $this->tipo = $ordemServico->tipo ?? '';
        $this->status = $ordemServico->status ?? '';
        $this->prioridade = $ordemServico->prioridade ?? '';
        $this->radio_link_id = $ordemServico->radio_link_id !== null ? (string) $ordemServico->radio_link_id : null;
        $this->estacao_a_id = $ordemServico->estacao_a_id !== null ? (string) $ordemServico->estacao_a_id : null;
        $this->estacao_b_id = $ordemServico->estacao_b_id !== null ? (string) $ordemServico->estacao_b_id : null;
        $this->solicitante = $ordemServico->solicitante ?? '';
        $this->responsavel_id = $ordemServico->responsavel_id !== null ? (string) $ordemServico->responsavel_id : null;
        $this->descricao = $ordemServico->descricao ?? '';
        $this->data_abertura = $ordemServico->data_abertura?->format('Y-m-d');
        $this->data_agendamento = $ordemServico->data_agendamento?->format('Y-m-d');
        $this->data_conclusao = $ordemServico->data_conclusao?->format('Y-m-d');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:ordens_servico,codigo,'.$this->ordemServico->id],
            'titulo' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', Rule::in(OrdemServico::TIPOS)],
            'status' => ['nullable', Rule::in(OrdemServico::STATUS)],
            'prioridade' => ['nullable', Rule::in(OrdemServico::PRIORIDADES)],
            'radio_link_id' => ['nullable', 'exists:radio_links,id'],
            'estacao_a_id' => ['nullable', 'exists:estacoes,id'],
            'estacao_b_id' => ['nullable', 'exists:estacoes,id'],
            'solicitante' => ['nullable', 'string', 'max:255'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
            'descricao' => ['nullable', 'string', 'max:5000'],
            'data_abertura' => ['nullable', 'date'],
            'data_agendamento' => ['nullable', 'date'],
            'data_conclusao' => ['nullable', 'date'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        $this->ordemServico->update($validated);

        Flux::toast(variant: 'success', text: __('Ordem de serviço atualizada com sucesso.'));

        $this->redirect(route('ordens-servico.index'), navigate: true);
    }

    public function updatedRadioLinkId(?string $value): void
    {
        if (! $value) {
            return;
        }

        $radioLink = RadioLink::with(['estacaoA', 'estacaoB'])->find($value);

        if ($radioLink) {
            $this->estacao_a_id = (string) $radioLink->estacao_a_id;
            $this->estacao_b_id = (string) $radioLink->estacao_b_id;
        }
    }

    public function render(): View
    {
        return view('livewire.ordens-servico.edit', [
            'radioLinks' => RadioLink::with(['estacaoA', 'estacaoB'])->orderBy('codigo')->get(),
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }
}
