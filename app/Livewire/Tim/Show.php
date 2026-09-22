<?php

namespace App\Livewire\Tim;

use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoAnexo;
use App\Models\TimProjetoEtapa;
use App\Models\TimProjetoHistorico;
use App\Models\TimRelatorio;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detalhes do Projeto TIM Implantação RF')]
class Show extends Component
{
    public ?TimProjeto $projeto = null;

    public string $buscaOs = '';

    public ?int $osParaVincular = null;

    public bool $showDeleteModal = false;

    public function mount(TimProjeto $projeto): void
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
        OrdemServico::where('projeto_tim_id', $this->projeto->id)->update(['projeto_tim_id' => null]);

        $anexos = $this->projeto->anexos()->pluck('arquivo')->all();

        $this->projeto->delete();

        Storage::disk('local')->delete($anexos);

        $this->redirect(route('tim.index'), navigate: true);
    }

    public function removerAnexo(TimProjetoAnexo $anexo): void
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

        $ordem->update(['projeto_tim_id' => $this->projeto->id]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_OS_VINCULADA,
            __('OS vinculada').' '.$ordem->codigo,
        );

        $this->osParaVincular = null;
        $this->buscaOs = '';

        $this->dispatch('tim-projeto-updated');
    }

    public function desvincular(int $ordemId): void
    {
        $ordem = OrdemServico::find($ordemId);

        if (! $ordem || $ordem->projeto_tim_id !== $this->projeto->id) {
            return;
        }

        $ordem->update(['projeto_tim_id' => null]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_OS_DESVINCULADA,
            __('OS desvinculada').' '.$ordem->codigo,
        );

        $this->dispatch('tim-projeto-updated');
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

    public function estacao(): ?Estacao
    {
        return $this->projeto->estacoes()->orderBy('site_id')->first();
    }

    /**
     * @return SupportCollection<int, OrdemServico>
     */
    #[Computed]
    public function ordensDisponiveis(): SupportCollection
    {
        return OrdemServico::query()
            ->whereNull('projeto_tim_id')
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
     * @return Collection<int, TimRelatorio>
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
     * @return Collection<int, TimProjetoEtapa>
     */
    #[Computed]
    public function etapas(): Collection
    {
        return $this->projeto->etapas()->get();
    }

    /**
     * @return Collection<int, TimProjetoHistorico>
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
        $etapa = TimProjetoEtapa::where('projeto_tim_id', $this->projeto->id)->find($etapaId);

        if (! $etapa) {
            return;
        }

        $ordem = array_search($etapa->status, TimProjetoEtapa::STATUS, true);
        $proximo = $ordem !== false && $ordem < count(TimProjetoEtapa::STATUS) - 1
            ? TimProjetoEtapa::STATUS[$ordem + 1]
            : $etapa->status;

        $anterior = $etapa->status;

        $etapa->update([
            'status' => $proximo,
            'data_conclusao' => $proximo === 'Concluída' ? now()->toDateString() : null,
        ]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_ETAPA_ALTERADA,
            $etapa->etapa.': '.$anterior.' → '.$proximo,
        );

        $this->dispatch('tim-etapa-updated');
    }

    public function retrocederEtapa(int $etapaId): void
    {
        $etapa = TimProjetoEtapa::where('projeto_tim_id', $this->projeto->id)->find($etapaId);

        if (! $etapa) {
            return;
        }

        $ordem = array_search($etapa->status, TimProjetoEtapa::STATUS, true);
        $anterior = $ordem !== false && $ordem > 0
            ? TimProjetoEtapa::STATUS[$ordem - 1]
            : $etapa->status;

        $statusAnterior = $etapa->status;

        $etapa->update([
            'status' => $anterior,
            'data_conclusao' => null,
        ]);

        $this->projeto->registrarHistorico(
            TimProjetoHistorico::TIPO_ETAPA_ALTERADA,
            $etapa->etapa.': '.$statusAnterior.' → '.$anterior,
        );

        $this->dispatch('tim-etapa-updated');
    }

    public function render(): View
    {
        return view('livewire.tim.show');
    }
}