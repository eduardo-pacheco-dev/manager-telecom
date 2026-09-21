<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use App\Models\OrdemServico;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Projeto Nokia')]
class Show extends Component
{
    public ?NokiaProjeto $projeto = null;

    public string $buscaOs = '';

    public ?int $osParaVincular = null;

    public bool $showDeleteModal = false;

    public function mount(NokiaProjeto $projeto): void
    {
        $this->projeto = $projeto;
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
        OrdemServico::where('projeto_nokia_id', $this->projeto->id)->update(['projeto_nokia_id' => null]);

        $this->projeto->delete();

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function vincular(int $ordemId): void
    {
        $ordem = OrdemServico::find($ordemId);

        if (! $ordem) {
            return;
        }

        $ordem->update(['projeto_nokia_id' => $this->projeto->id]);

        $this->osParaVincular = null;
        $this->buscaOs = '';

        $this->dispatch('nokia-projeto-updated');
    }

    public function desvincular(int $ordemId): void
    {
        $ordem = OrdemServico::find($ordemId);

        if (! $ordem || $ordem->projeto_nokia_id !== $this->projeto->id) {
            return;
        }

        $ordem->update(['projeto_nokia_id' => null]);

        $this->dispatch('nokia-projeto-updated');
    }

    /**
     * @return Collection<int, OrdemServico>
     */
    #[Computed]
    public function ordens(): Collection
    {
        return $this->projeto->ordensServico()
            ->with(['radioLink', 'estacaoA', 'estacaoB'])
            ->orderBy('codigo')
            ->get();
    }

    /**
     * @return SupportCollection<int, OrdemServico>
     */
    #[Computed]
    public function ordensDisponiveis(): SupportCollection
    {
        return OrdemServico::query()
            ->whereNull('projeto_nokia_id')
            ->when($this->buscaOs !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->buscaOs}%")
                        ->orWhere('titulo', 'like', "%{$this->buscaOs}%");
                });
            })
            ->orderBy('codigo')
            ->limit(20)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.nokia.show');
    }
}
