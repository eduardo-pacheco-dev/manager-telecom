<?php

namespace App\Livewire\Nokia;

use App\Models\Estacao;
use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoHistorico;
use App\Models\NokiaRelatorio;
use App\Models\OrdemServico;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Relatório Nokia')]
class RelatorioCreate extends Component
{
    public ?NokiaProjeto $projeto = null;

    public ?string $ordem_servico_id = null;

    public ?string $estacao_id = null;

    public ?string $data_inicio = null;

    public ?string $data_planejada = null;

    public ?string $data_real = null;

    public string $status = 'Pendente';

    public string $observacao = '';

    public bool $ativo = true;

    public function mount(NokiaProjeto $projeto): void
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
            'status' => ['required', Rule::in(NokiaRelatorio::STATUS)],
            'observacao' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        $relatorio = NokiaRelatorio::create([
            'projeto_nokia_id' => $this->projeto->id,
            ...$validated,
        ]);

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_RELATORIO_CRIADO,
            __('Relatório criado').' '.($relatorio->ordemServico?->codigo ?: '#'.$relatorio->id),
        );

        Flux::toast(variant: 'success', text: __('Relatório Nokia criado com sucesso.'));

        $this->redirect(route('nokia.relatorios.show', [$this->projeto, $relatorio]), navigate: true);
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
        return view('livewire.nokia.relatorio-create');
    }
}
