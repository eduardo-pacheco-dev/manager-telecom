<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoHistorico;
use App\Models\NokiaRelatorio;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Relatório Nokia')]
class RelatorioShow extends Component
{
    public ?NokiaProjeto $projeto = null;

    public ?NokiaRelatorio $relatorio = null;

    public bool $showDeleteModal = false;

    public function mount(NokiaProjeto $projeto, NokiaRelatorio $relatorio): void
    {
        abort_unless($relatorio->projeto_nokia_id === $projeto->id, 404);

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
            NokiaProjetoHistorico::TIPO_RELATORIO_EXCLUIDO,
            __('Relatório excluído').' '.($this->relatorio->ordemServico?->codigo ?: '#'.$this->relatorio->id),
        );

        $this->relatorio->delete();

        $this->redirect(route('nokia.show', $this->projeto), navigate: true);
    }

    public function atualizarStatus(string $status): void
    {
        if (! in_array($status, NokiaRelatorio::STATUS, true)) {
            return;
        }

        $this->relatorio->update([
            'status' => $status,
            'data_real' => $status === 'Concluído' && $this->relatorio->data_real === null
                ? now()->toDateString()
                : $this->relatorio->data_real,
        ]);

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_RELATORIO_CRIADO,
            __('Relatório').' '.($this->relatorio->ordemServico?->codigo ?: '#'.$this->relatorio->id).' → '.$status,
        );

        $this->dispatch('nokia-relatorio-updated');
    }

    public function render(): View
    {
        $this->relatorio->load(['ordemServico', 'estacao']);

        return view('livewire.nokia.relatorio-show');
    }
}
