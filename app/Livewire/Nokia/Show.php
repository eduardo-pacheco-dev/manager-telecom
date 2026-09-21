<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoAnexo;
use App\Models\NokiaProjetoEtapa;
use App\Models\NokiaProjetoHistorico;
use App\Models\NokiaRelatorio;
use App\Models\OrdemServico;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Storage;
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

        $anexos = $this->projeto->anexos()->pluck('arquivo')->all();

        $this->projeto->delete();

        Storage::disk('local')->delete($anexos);

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function removerAnexo(NokiaProjetoAnexo $anexo): void
    {
        Storage::disk('local')->delete($anexo->arquivo);

        $anexo->delete();

        Flux::toast(variant: 'success', text: __('Anexo removido com sucesso.'));
    }

    public function vincular(int $ordemId): void
    {
        $ordem = OrdemServico::find($ordemId);

        if (! $ordem) {
            return;
        }

        $ordem->update(['projeto_nokia_id' => $this->projeto->id]);

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_OS_VINCULADA,
            __('OS vinculada').' '.$ordem->codigo,
        );

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

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_OS_DESVINCULADA,
            __('OS desvinculada').' '.$ordem->codigo,
        );

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

    /**
     * @return Collection<int, NokiaRelatorio>
     */
    #[Computed]
    public function relatorios(): Collection
    {
        return $this->projeto->relatorios()
            ->with(['ordemServico', 'estacao'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @return Collection<int, NokiaProjetoEtapa>
     */
    #[Computed]
    public function etapas(): Collection
    {
        return $this->projeto->etapas()->get();
    }

    /**
     * @return Collection<int, NokiaProjetoHistorico>
     */
    #[Computed]
    public function historicos(): Collection
    {
        return $this->projeto->historicos()
            ->with('user')
            ->limit(50)
            ->get();
    }

    public function avancarEtapa(int $etapaId): void
    {
        $etapa = NokiaProjetoEtapa::where('projeto_nokia_id', $this->projeto->id)->find($etapaId);

        if (! $etapa) {
            return;
        }

        $ordem = array_search($etapa->status, NokiaProjetoEtapa::STATUS, true);
        $proximo = $ordem !== false && $ordem < count(NokiaProjetoEtapa::STATUS) - 1
            ? NokiaProjetoEtapa::STATUS[$ordem + 1]
            : $etapa->status;

        $anterior = $etapa->status;

        $etapa->update([
            'status' => $proximo,
            'data_conclusao' => $proximo === 'Concluída' ? now()->toDateString() : null,
        ]);

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_ETAPA_ALTERADA,
            $etapa->etapa.': '.$anterior.' → '.$proximo,
        );

        $this->dispatch('nokia-etapa-updated');
    }

    public function retrocederEtapa(int $etapaId): void
    {
        $etapa = NokiaProjetoEtapa::where('projeto_nokia_id', $this->projeto->id)->find($etapaId);

        if (! $etapa) {
            return;
        }

        $ordem = array_search($etapa->status, NokiaProjetoEtapa::STATUS, true);
        $anterior = $ordem !== false && $ordem > 0
            ? NokiaProjetoEtapa::STATUS[$ordem - 1]
            : $etapa->status;

        $statusAnterior = $etapa->status;

        $etapa->update([
            'status' => $anterior,
            'data_conclusao' => null,
        ]);

        $this->projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_ETAPA_ALTERADA,
            $etapa->etapa.': '.$statusAnterior.' → '.$anterior,
        );

        $this->dispatch('nokia-etapa-updated');
    }

    public function render(): View
    {
        return view('livewire.nokia.show');
    }
}
