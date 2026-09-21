<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoEtapa;
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

    public ?string $baseline_mos = null;

    public ?string $planejada_mos = null;

    public ?string $real_mos = null;

    public ?string $baseline_instalacao = null;

    public ?string $planejada_instalacao = null;

    public ?string $real_instalacao = null;

    public ?string $baseline_integracao = null;

    public ?string $planejada_integracao = null;

    public ?string $real_integracao = null;

    public ?string $baseline_rfa = null;

    public ?string $planejada_rfa = null;

    public ?string $real_rfa = null;

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

        foreach ($projeto->etapas as $etapa) {
            match ($etapa->etapa) {
                'MOS' => $this->preencherCronogramaEtapa($etapa),
                'Instalação' => $this->preencherCronogramaEtapa($etapa, 'instalacao'),
                'Integração' => $this->preencherCronogramaEtapa($etapa, 'integracao'),
                'RFA' => $this->preencherCronogramaEtapa($etapa, 'rfa'),
                default => null,
            };
        }
    }

    private function preencherCronogramaEtapa(NokiaProjetoEtapa $etapa, string $sufixo = 'mos'): void
    {
        $this->{'baseline_'.$sufixo} = $etapa->data_baseline?->format('Y-m-d');
        $this->{'planejada_'.$sufixo} = $etapa->data_planejada?->format('Y-m-d');
        $this->{'real_'.$sufixo} = $etapa->data_real?->format('Y-m-d');
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
            'baseline_mos' => ['nullable', 'date'],
            'planejada_mos' => ['nullable', 'date'],
            'real_mos' => ['nullable', 'date'],
            'baseline_instalacao' => ['nullable', 'date'],
            'planejada_instalacao' => ['nullable', 'date'],
            'real_instalacao' => ['nullable', 'date'],
            'baseline_integracao' => ['nullable', 'date'],
            'planejada_integracao' => ['nullable', 'date'],
            'real_integracao' => ['nullable', 'date'],
            'baseline_rfa' => ['nullable', 'date'],
            'planejada_rfa' => ['nullable', 'date'],
            'real_rfa' => ['nullable', 'date'],
            'ativo' => ['boolean'],
        ]);

        $this->projeto->update($validated);

        $this->projeto->atualizarCronogramaEtapas([
            'MOS' => [
                'baseline' => $this->baseline_mos,
                'planejada' => $this->planejada_mos,
                'real' => $this->real_mos,
            ],
            'Instalação' => [
                'baseline' => $this->baseline_instalacao,
                'planejada' => $this->planejada_instalacao,
                'real' => $this->real_instalacao,
            ],
            'Integração' => [
                'baseline' => $this->baseline_integracao,
                'planejada' => $this->planejada_integracao,
                'real' => $this->real_integracao,
            ],
            'RFA' => [
                'baseline' => $this->baseline_rfa,
                'planejada' => $this->planejada_rfa,
                'real' => $this->real_rfa,
            ],
        ]);

        Flux::toast(variant: 'success', text: __('Projeto Nokia atualizado com sucesso.'));

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.nokia.edit');
    }
}
