<?php

namespace App\Livewire\Tim;

use App\Models\TimProjeto;
use App\Models\TimProjetoHistorico;
use App\Models\TimRelatorio;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Relatório TIM Implantação RF')]
class RelatorioShow extends Component
{
    public ?TimProjeto $projeto = null;

    public ?TimRelatorio $relatorio = null;

    public bool $showDeleteModal = false;

    public function mount(TimProjeto $projeto, TimRelatorio $relatorio): void
    {
        abort_unless($relatorio->projeto_tim_id === $projeto->id, 404);

        $this->projeto = $projeto;
        $this->relatorio = $relatorio;
    }

    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    public function destroy(): void
    {
        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_RELATORIO_EXCLUIDO,
            __('Relatório excluído').' '.($this->relatorio->ordemServico?->codigo ?: '#'.$this->relatorio->id),
        );

        $this->relatorio->delete();

        $this->redirect(route('tim.show', $this->projeto), navigate: true);
    }

    public function atualizarStatus(string $status): void
    {
        if (! in_array($status, TimRelatorio::STATUS, true)) {
            return;
        }

        $this->relatorio->update([
            'status' => $status,
            'data_real' => $status === 'Concluído' && $this->relatorio->data_real === null
                ? now()->toDateString()
                : $this->relatorio->data_real,
        ]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_RELATORIO_CRIADO,
            __('Relatório').' '.($this->relatorio->ordemServico?->codigo ?: '#'.$this->relatorio->id).' → '.$status,
        );

        $this->dispatch('tim-relatorio-updated');
    }

    public function render(): View
    {
        $this->relatorio->load(['ordemServico', 'estacao']);

        return view('livewire.tim.relatorio-show');
    }
}