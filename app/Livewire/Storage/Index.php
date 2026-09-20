<?php

namespace App\Livewire\Storage;

use App\Models\Estacao;
use App\Models\EstacaoAnexo;
use App\Models\OrdemServico;
use App\Models\OrdemServicoAnexo;
use App\Models\RadioLinkAnexo;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Storage')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public bool $showUploadModal = false;

    public ?int $estacaoId = null;

    public ?int $ordemServicoId = null;

    public $arquivo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function abrirUploadEstacao(Estacao $estacao): void
    {
        $this->estacaoId = $estacao->id;
        $this->ordemServicoId = null;
        $this->arquivo = null;
        $this->showUploadModal = true;
    }

    public function abrirUploadOrdem(OrdemServico $ordemServico): void
    {
        $this->ordemServicoId = $ordemServico->id;
        $this->estacaoId = null;
        $this->arquivo = null;
        $this->showUploadModal = true;
    }

    public function fecharUpload(): void
    {
        $this->showUploadModal = false;
        $this->reset(['estacaoId', 'ordemServicoId', 'arquivo']);
    }

    public function salvarArquivo(): void
    {
        $this->validate([
            'arquivo' => ['required', 'file', 'max:20480'],
        ], [
            'arquivo.required' => __('Escolha um arquivo para enviar.'),
            'arquivo.file' => __('O valor deve ser um arquivo.'),
            'arquivo.max' => __('O arquivo não pode ter mais de 20 MB.'),
        ]);

        if ($this->ordemServicoId !== null) {
            $ordem = OrdemServico::findOrFail($this->ordemServicoId);
            $destino = 'anexos/ordem-servico/'.$ordem->id;
            $caminho = $this->arquivo->storeAs(
                $destino,
                Str::uuid().'.'.$this->arquivo->getClientOriginalExtension(),
                'local',
            );

            $ordem->anexos()->create([
                'nome' => $this->arquivo->getClientOriginalName(),
                'arquivo' => $caminho,
                'mime' => $this->arquivo->getMimeType(),
                'tamanho' => $this->arquivo->getSize(),
            ]);
        } else {
            $estacao = Estacao::findOrFail($this->estacaoId);
            $destino = 'anexos/estacao/'.$estacao->id;
            $caminho = $this->arquivo->storeAs(
                $destino,
                Str::uuid().'.'.$this->arquivo->getClientOriginalExtension(),
                'local',
            );

            $estacao->anexos()->create([
                'nome' => $this->arquivo->getClientOriginalName(),
                'arquivo' => $caminho,
                'mime' => $this->arquivo->getMimeType(),
                'tamanho' => $this->arquivo->getSize(),
            ]);
        }

        $this->fecharUpload();

        $this->dispatch('flux-toast', text: __('Arquivo enviado com sucesso.'), variant: 'success');
    }

    public function removerAnexoEstacao(EstacaoAnexo $anexo): void
    {
        Storage::disk('local')->delete($anexo->arquivo);
        $anexo->delete();

        $this->dispatch('flux-toast', text: __('Anexo removido.'), variant: 'success');
    }

    public function removerAnexoOrdem(OrdemServicoAnexo $anexo): void
    {
        Storage::disk('local')->delete($anexo->arquivo);
        $anexo->delete();

        $this->dispatch('flux-toast', text: __('Anexo removido.'), variant: 'success');
    }

    public function removerAnexoRadioLink(RadioLinkAnexo $anexo): void
    {
        Storage::disk('local')->delete($anexo->arquivo);
        $anexo->delete();

        $this->dispatch('flux-toast', text: __('Anexo removido.'), variant: 'success');
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'arquivos' => EstacaoAnexo::count() + OrdemServicoAnexo::count() + RadioLinkAnexo::count(),
            'tamanho' => (int) (EstacaoAnexo::sum('tamanho') + OrdemServicoAnexo::sum('tamanho') + RadioLinkAnexo::sum('tamanho')),
            'estacoes' => Estacao::query()->count(),
            'ordens' => OrdemServico::query()->count(),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Estacao>
     */
    public function estacoes(): LengthAwarePaginator
    {
        return Estacao::query()
            ->with([
                'anexos',
                'radioLinksA.anexos',
                'radioLinksB.anexos',
                'ordensServicoA.anexos',
                'ordensServicoB.anexos',
            ])
            ->when($this->search, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('site_id', 'like', "%{$search}%")
                        ->orWhere('municipio', 'like', "%{$search}%")
                        ->orWhereHas('anexos', fn ($a) => $a->where('nome', 'like', "%{$search}%"))
                        ->orWhereHas('ordensServicoA', fn ($o) => $o->where('codigo', 'like', "%{$search}%"));
                });
            })
            ->orderBy('site_id')
            ->paginate($this->perPage);
    }

    public function render(): View
    {
        return view('livewire.storage.index', [
            'estacoes' => $this->estacoes(),
        ]);
    }
}
