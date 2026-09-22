<?php

namespace App\Livewire\Tim;

use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoHistorico;
use App\Models\TimRelatorio;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Relatório TIM Implantação RF')]
class RelatorioCreate extends Component
{
    public ?TimProjeto $projeto = null;

    public ?string $ordem_servico_id = null;

    public ?string $estacao_id = null;

    public ?string $data_inicio = null;

    public ?string $data_planejada = null;

    public ?string $data_real = null;

    public string $status = 'Pendente';

    public string $observacao = '';

    public bool $ativo = true;

    public function mount(TimProjeto $projeto): void
    {
        $this->projeto = $projeto;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'ordem_servico_id' => ['required', 'exists:ordens_servico,id'],
            'estacao_id' => ['required', 'exists:estacoes,id'],
            'data_inicio' => ['nullable', 'date'],
            'data_planejada' => ['nullable', 'date'],
            'data_real' => ['nullable', 'date'],
            'status' => ['required', Rule::in(TimRelatorio::STATUS)],
            'observacao' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        $relatorio = TimRelatorio::create([
            'projeto_tim_id' => $this->projeto->id,
            ...$validated,
        ]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_RELATORIO_CRIADO,
            __('Relatório criado').' '.($relatorio->ordemServico?->codigo ?: '#'.$relatorio->id),
        );

        Flux::toast(variant: 'success', text: __('Relatório TIM Implantação RF criado com sucesso.'));

        $this->redirect(route('tim.relatorios.show', [$this->projeto, $relatorio]), navigate: true);
    }

    /**
     * @return Collection<int, OrdemServico>
     */
    #[Computed]
    public function ordensDisponiveis(): Collection
    {
        return OrdemServico::query()
            ->orderBy('codigo')
            ->get();
    }

    /**
     * @return Collection<int, Estacao>
     */
    #[Computed]
    public function estacoesDisponiveis(): Collection
    {
        return Estacao::query()
            ->orderBy('site_id')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.tim.relatorio-create');
    }
}