<?php

namespace App\Livewire\OrdensServico;

use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\RadioLink;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Nova Ordem de Serviço')]
class Create extends Component
{
    public string $codigo = '';

    public string $titulo = '';

    public string $tipo = '';

    public string $escopo = 'Enlace';

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

    public function mount(): void
    {
        $this->codigo = $this->gerarCodigo();
    }

    public function gerarCodigo(): string
    {
        $ultimo = OrdemServico::query()
            ->where('codigo', 'like', 'OS-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'OS-'))
            ->max() ?? 0;

        return 'OS-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:ordens_servico,codigo'],
            'titulo' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', Rule::in(OrdemServico::tiposDisponiveis())],
            'escopo' => ['nullable', Rule::in(OrdemServico::ESCOPOS)],
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

        $escopo = $validated['escopo'] ?? null;

        $this->validate([
            'radio_link_id' => $escopo === 'Enlace' ? ['required', 'exists:radio_links,id'] : ['nullable'],
            'estacao_a_id' => $escopo === 'Estação' ? ['required', 'exists:estacoes,id'] : ['nullable'],
            'estacao_b_id' => $escopo === 'Enlace' ? ['required', 'exists:estacoes,id'] : ['nullable'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        OrdemServico::create($validated);

        Flux::toast(variant: 'success', text: __('Ordem de serviço criada com sucesso.'));

        $this->redirect(route('ordens-servico.index'), navigate: true);
    }

    public function updatedEscopo(?string $value): void
    {
        if ($value === 'Enlace') {
            $this->reset(['estacao_a_id', 'estacao_b_id']);
        }

        if ($value === 'Estação') {
            $this->reset(['radio_link_id', 'estacao_b_id']);
        }

        if ($value === 'Outro') {
            $this->reset(['radio_link_id', 'estacao_a_id', 'estacao_b_id']);
        }
    }

    public function updatedRadioLinkId(?string $value): void
    {
        if (! $value) {
            return;
        }

        $radioLink = RadioLink::with(['estacaoA', 'estacaoB'])->find($value);

        if ($radioLink) {
            $this->escopo = 'Enlace';
            $this->estacao_a_id = (string) $radioLink->estacao_a_id;
            $this->estacao_b_id = (string) $radioLink->estacao_b_id;
            $this->titulo = $this->titulo === ''
                ? 'Serviço no link '.$radioLink->codigo
                : $this->titulo;
        }
    }

    public function updatedEstacaoAId(?string $value): void
    {
        if (! $value) {
            return;
        }

        $estacao = Estacao::find($value);

        if ($estacao) {
            $this->escopo = 'Estação';
            $this->titulo = $this->titulo === ''
                ? 'Serviço na estação '.$estacao->site_id
                : $this->titulo;
        }
    }

    public function render(): View
    {
        return view('livewire.ordens-servico.create', [
            'radioLinks' => RadioLink::with(['estacaoA', 'estacaoB'])->orderBy('codigo')->get(),
            'estacoes' => Estacao::orderBy('site_id')->get(),
            'responsaveis' => User::orderBy('name')->get(),
        ]);
    }
}
