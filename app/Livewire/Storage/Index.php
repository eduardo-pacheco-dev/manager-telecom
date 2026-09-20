<?php

namespace App\Livewire\Storage;

use App\Models\Estacao;
use App\Models\EstacaoAnexo;
use App\Models\OrdemServico;
use App\Models\OrdemServicoAnexo;
use App\Models\RadioLink;
use App\Models\RadioLinkAnexo;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Armazenamento')]
class Index extends Component
{
    use WithFileUploads;

    public ?int $estacaoId = null;

    public ?int $ordemServicoId = null;

    public ?int $radioLinkId = null;

    public string $view = 'lista';

    public string $search = '';

    public string $sortField = 'nome';

    public string $sortDirection = 'asc';

    public bool $showUploadModal = false;

    public bool $showArvore = true;

    /** @var array<int, string> */
    public array $expandidos = [];

    public $arquivo = null;

    public function abrirEstacao(int $id): void
    {
        $this->estacaoId = $id;
        $this->ordemServicoId = null;
        $this->radioLinkId = null;
        $this->expandir('estacao-'.$id);
    }

    public function abrirOrdem(int $id): void
    {
        $this->ordemServicoId = $id;
        $this->radioLinkId = null;
        $this->expandir('ordem-'.$id);
    }

    public function abrirRadioLink(int $id): void
    {
        $this->radioLinkId = $id;
        $this->ordemServicoId = null;
        $this->expandir('radio-'.$id);
    }

    public function voltar(): void
    {
        if ($this->ordemServicoId !== null || $this->radioLinkId !== null) {
            $this->ordemServicoId = null;
            $this->radioLinkId = null;

            return;
        }

        $this->estacaoId = null;
    }

    public function voltarRaiz(): void
    {
        $this->estacaoId = null;
        $this->ordemServicoId = null;
        $this->radioLinkId = null;
    }

    public function alternarArvore(): void
    {
        $this->showArvore = ! $this->showArvore;
    }

    public function alternarExpandido(string $chave): void
    {
        if (in_array($chave, $this->expandidos, true)) {
            $this->expandidos = array_values(array_diff($this->expandidos, [$chave]));
        } else {
            $this->expandidos[] = $chave;
        }
    }

    private function expandir(string $chave): void
    {
        if (! in_array($chave, $this->expandidos, true)) {
            $this->expandidos[] = $chave;
        }
    }

    public function alternarView(): void
    {
        $this->view = $this->view === 'lista' ? 'grade' : 'lista';
    }

    public function ordenar(string $campo): void
    {
        if (! in_array($campo, ['nome', 'data', 'tamanho'], true)) {
            return;
        }

        if ($this->sortField === $campo) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $campo;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function breadcrumbs(): array
    {
        $crumb = [['label' => __('Armazenamento'), 'acao' => 'voltarRaiz']];

        if ($this->estacaoId === null) {
            return $crumb;
        }

        $estacao = Estacao::find($this->estacaoId);
        $crumb[] = ['label' => $estacao?->site_id ?? __('Estação'), 'acao' => 'voltar'];

        if ($this->ordemServicoId !== null) {
            $os = OrdemServico::find($this->ordemServicoId);
            $crumb[] = ['label' => $os?->codigo ?? __('Ordem'), 'acao' => null];
        } elseif ($this->radioLinkId !== null) {
            $rl = RadioLink::find($this->radioLinkId);
            $crumb[] = ['label' => $rl?->codigo ?? __('Radio Link'), 'acao' => null];
        }

        return $crumb;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    #[Computed]
    public function itens(): Collection
    {
        $itens = $this->coletarItens();

        if ($this->search !== '') {
            $busca = mb_strtolower(trim($this->search));
            $itens = $itens->filter(fn (array $item) => str_contains(mb_strtolower($item['nome']), $busca));
        }

        return $this->ordenarItens($itens);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    #[Computed]
    public function arvore(): Collection
    {
        return Estacao::query()
            ->with([
                'anexos',
                'radioLinksA.anexos',
                'radioLinksB.anexos',
                'ordensServicoA.anexos',
                'ordensServicoB.anexos',
            ])
            ->when($this->search !== '', function ($query) {
                $busca = mb_strtolower(trim($this->search));
                $query->where(function ($q) use ($busca) {
                    $q->where('site_id', 'like', "%{$busca}%")
                        ->orWhere('municipio', 'like', "%{$busca}%")
                        ->orWhereHas('anexos', fn ($a) => $a->whereRaw('LOWER(nome) LIKE ?', ["%{$busca}%"]))
                        ->orWhereHas('radioLinksA', fn ($r) => $r->where('codigo', 'like', "%{$busca}%"))
                        ->orWhereHas('radioLinksB', fn ($r) => $r->where('codigo', 'like', "%{$busca}%"))
                        ->orWhereHas('ordensServicoA', fn ($o) => $o->where('codigo', 'like', "%{$busca}%"))
                        ->orWhereHas('ordensServicoB', fn ($o) => $o->where('codigo', 'like', "%{$busca}%"));
                });
            })
            ->orderBy('site_id')
            ->get()
            ->map(function (Estacao $estacao) {
                $filhos = collect();

                foreach ($estacao->radioLinksRelacionados() as $rl) {
                    if ($this->search !== '' && ! str_contains(mb_strtolower($rl->codigo), mb_strtolower($this->search))) {
                        continue;
                    }

                    $filhos->push([
                        'tipo' => 'radio',
                        'id' => $rl->id,
                        'nome' => $rl->codigo,
                        'subtitulo' => $rl->status ?: '—',
                        'contagem' => $rl->anexos->count(),
                    ]);
                }

                foreach ($estacao->ordensServicoRelacionadas() as $os) {
                    if ($this->search !== '' && ! str_contains(mb_strtolower($os->codigo), mb_strtolower($this->search))) {
                        continue;
                    }

                    $filhos->push([
                        'tipo' => 'ordem',
                        'id' => $os->id,
                        'nome' => $os->codigo,
                        'subtitulo' => $os->titulo ?: '—',
                        'contagem' => $os->anexos->count(),
                    ]);
                }

                return [
                    'tipo' => 'estacao',
                    'id' => $estacao->id,
                    'nome' => $estacao->site_id,
                    'subtitulo' => $estacao->municipio ?: '—',
                    'contagem' => $estacao->anexos->count() + $filhos->sum('contagem'),
                    'filhos' => $filhos,
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function coletarItens(): Collection
    {
        if ($this->estacaoId === null) {
            return Estacao::query()
                ->withCount(['anexos', 'radioLinksA', 'radioLinksB', 'ordensServicoA', 'ordensServicoB'])
                ->orderBy('site_id')
                ->get()
                ->map(function (Estacao $estacao) {
                    $arquivos = $estacao->anexos_count
                        + $estacao->radio_links_a_count
                        + $estacao->radio_links_b_count
                        + $estacao->ordens_servico_a_count
                        + $estacao->ordens_servico_b_count;

                    return [
                        'tipo' => 'estacao',
                        'id' => $estacao->id,
                        'nome' => $estacao->site_id,
                        'subtitulo' => $estacao->municipio ?: '—',
                        'data' => $estacao->updated_at,
                        'tamanho' => $arquivos,
                        'mime' => null,
                        'abrir' => 'abrirEstacao('.$estacao->id.')',
                    ];
                })
                ->values();
        }

        if ($this->ordemServicoId !== null) {
            $os = OrdemServico::with('anexos')->findOrFail($this->ordemServicoId);

            return $os->anexos
                ->map(fn ($anexo) => $this->montarArquivo('arquivo_ordem', $anexo, route('ordens-servico.anexos.download', $anexo), 'removerAnexoOrdem('.$anexo->id.')'))
                ->values();
        }

        if ($this->radioLinkId !== null) {
            $rl = RadioLink::with('anexos')->findOrFail($this->radioLinkId);

            return $rl->anexos
                ->map(fn ($anexo) => $this->montarArquivo('arquivo_radio', $anexo, route('radio-links.anexos.download', $anexo), 'removerAnexoRadioLink('.$anexo->id.')'))
                ->values();
        }

        $estacao = Estacao::with([
            'anexos',
            'radioLinksA',
            'radioLinksB',
            'ordensServicoA',
            'ordensServicoB',
        ])->findOrFail($this->estacaoId);

        $itens = collect();

        foreach ($estacao->anexos as $anexo) {
            $itens->push($this->montarArquivo('arquivo_estacao', $anexo, route('estacoes.anexos.download', $anexo), 'removerAnexoEstacao('.$anexo->id.')'));
        }

        foreach ($estacao->radioLinksRelacionados() as $rl) {
            $itens->push([
                'tipo' => 'radio',
                'id' => $rl->id,
                'nome' => $rl->codigo,
                'subtitulo' => $rl->status ?: '—',
                'data' => $rl->updated_at,
                'tamanho' => $rl->anexos->count(),
                'mime' => null,
                'abrir' => 'abrirRadioLink('.$rl->id.')',
            ]);
        }

        foreach ($estacao->ordensServicoRelacionadas() as $os) {
            $itens->push([
                'tipo' => 'ordem',
                'id' => $os->id,
                'nome' => $os->codigo,
                'subtitulo' => $os->titulo ?: '—',
                'data' => $os->updated_at,
                'tamanho' => $os->anexos->count(),
                'mime' => null,
                'abrir' => 'abrirOrdem('.$os->id.')',
            ]);
        }

        return $itens;
    }

    /**
     * @return array<string, mixed>
     */
    private function montarArquivo(string $tipo, object $anexo, string $download, string $remover): array
    {
        return [
            'tipo' => $tipo,
            'id' => $anexo->id,
            'nome' => $anexo->nome,
            'subtitulo' => null,
            'data' => $anexo->created_at,
            'tamanho' => $anexo->tamanho,
            'mime' => $anexo->mime,
            'download' => $download,
            'remover' => $remover,
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $itens
     * @return Collection<int, array<string, mixed>>
     */
    private function ordenarItens(Collection $itens): Collection
    {
        $campo = $this->sortField;
        $direcao = $this->sortDirection;

        $sorted = $itens->sortBy(
            fn (array $item) => $campo === 'tamanho'
                ? (int) ($item['tamanho'] ?? 0)
                : ($campo === 'data'
                    ? optional($item['data'])->timestamp ?? 0
                    : mb_strtolower($item['nome'])),
            SORT_REGULAR,
            $direcao === 'desc',
        );

        return $sorted->values();
    }

    public function abrirUpload(): void
    {
        $this->arquivo = null;
        $this->showUploadModal = true;
    }

    public function abrirUploadEstacao(Estacao $estacao): void
    {
        $this->estacaoId = $estacao->id;
        $this->ordemServicoId = null;
        $this->radioLinkId = null;
        $this->arquivo = null;
        $this->showUploadModal = true;
    }

    public function abrirUploadOrdem(OrdemServico $ordemServico): void
    {
        $this->ordemServicoId = $ordemServico->id;
        $this->radioLinkId = null;
        $this->arquivo = null;
        $this->showUploadModal = true;
    }

    public function fecharUpload(): void
    {
        $this->showUploadModal = false;
        $this->reset('arquivo');
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
            $caminho = $this->arquivo->storeAs($destino, Str::uuid().'.'.$this->arquivo->getClientOriginalExtension(), 'local');

            $ordem->anexos()->create([
                'nome' => $this->arquivo->getClientOriginalName(),
                'arquivo' => $caminho,
                'mime' => $this->arquivo->getMimeType(),
                'tamanho' => $this->arquivo->getSize(),
            ]);
        } elseif ($this->radioLinkId !== null) {
            $radioLink = RadioLink::findOrFail($this->radioLinkId);
            $destino = 'anexos/radio-link/'.$radioLink->id;
            $caminho = $this->arquivo->storeAs($destino, Str::uuid().'.'.$this->arquivo->getClientOriginalExtension(), 'local');

            $radioLink->anexos()->create([
                'nome' => $this->arquivo->getClientOriginalName(),
                'arquivo' => $caminho,
                'mime' => $this->arquivo->getMimeType(),
                'tamanho' => $this->arquivo->getSize(),
            ]);
        } else {
            $estacao = Estacao::findOrFail($this->estacaoId);
            $destino = 'anexos/estacao/'.$estacao->id;
            $caminho = $this->arquivo->storeAs($destino, Str::uuid().'.'.$this->arquivo->getClientOriginalExtension(), 'local');

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

    public function render(): View
    {
        return view('livewire.storage.index');
    }
}
