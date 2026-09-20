<?php

namespace App\Livewire\Produtos;

use App\Jobs\ProcessProdutoImport;
use App\Models\Produto;
use App\Models\ProdutoImport;
use App\Services\ExcelExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Produtos')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = [
        'nome', 'codigo', 'categoria', 'preco', 'ativo',
    ];

    public string $search = '';

    public string $filtroCategoria = '';

    public string $filtroStatus = '';

    public string $sortField = 'nome';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $produtoParaExcluir = null;

    public bool $showImportModal = false;

    public ?TemporaryUploadedFile $import_arquivo = null;

    /** @var array<int, string> */
    public array $importesStatus = [];

    /** @var array<int, int> */
    public array $selecionados = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $campo): void
    {
        if (! in_array($campo, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $campo) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $campo;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function toggleAtivo(Produto $produto): void
    {
        $produto->update(['ativo' => ! $produto->ativo]);

        $this->dispatch('produto-updated');
    }

    public function destroy(Produto $produto): void
    {
        $produto->delete();

        $this->limparSelecao();

        $this->dispatch('produto-deleted');
    }

    public function abrirImportacao(): void
    {
        $this->showImportModal = true;
    }

    public function fecharImportacao(): void
    {
        $this->showImportModal = false;
        $this->reset('import_arquivo');
    }

    public function limparArquivoImportacao(): void
    {
        $this->reset('import_arquivo');
    }

    public function iniciarImportacao(): void
    {
        $this->validate([
            'import_arquivo' => ['required', 'file', 'max:204800', 'mimes:xlsx,csv'],
        ], [
            'import_arquivo.required' => __('Escolha um arquivo Excel para importar.'),
            'import_arquivo.file' => __('O valor deve ser um arquivo.'),
            'import_arquivo.max' => __('O arquivo nÃ£o pode ter mais de 200 MB.'),
            'import_arquivo.mimes' => __('O arquivo deve ser um Excel (.xlsx) ou CSV.'),
        ]);

        $caminho = $this->import_arquivo->store(
            'imports/produto',
            'local',
        );

        $import = ProdutoImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => ProdutoImport::STATUS_PENDENTE,
        ]);

        ProcessProdutoImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o iniciada. Os produtos serÃ£o importados em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = ProdutoImport::query()
            ->latest()
            ->limit(5)
            ->get();

        foreach ($importacoes as $importacao) {
            $statusAnterior = $this->importesStatus[$importacao->id] ?? null;

            if ($statusAnterior === $importacao->status) {
                continue;
            }

            if ($statusAnterior === null) {
                $this->importesStatus[$importacao->id] = $importacao->status;

                continue;
            }

            $this->importesStatus[$importacao->id] = $importacao->status;

            if ($importacao->status === ProdutoImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o concluÃ­da: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === ProdutoImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === ProdutoImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum produto selecionado.'));
        }

        $produtos = Produto::whereIn('id', $this->selecionados)
            ->orderBy('nome')
            ->get();

        return $exporter->download(
            'produtos-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($produtos),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryProdutos();

        return $exporter->download(
            'produtos.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($query->orderBy('nome')->get()),
        );
    }

    /**
     * @return array<int, string>
     */
    private function cabecalhoExportacao(): array
    {
        return [
            'Nome', 'CÃ³digo', 'Categoria', 'DescriÃ§Ã£o', 'PreÃ§o', 'Status',
        ];
    }

    /**
     * @param  Collection<int, Produto>  $produtos
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $produtos): array
    {
        return $produtos->map(function (Produto $produto): array {
            return [
                $produto->nome,
                $produto->codigo,
                $produto->categoria,
                $produto->descricao,
                $produto->preco !== null ? (float) $produto->preco : null,
                $produto->ativo ? 'Ativo' : 'Inativo',
            ];
        })->all();
    }

    public function alternarSelecao(int $id): void
    {
        if (in_array($id, $this->selecionados, true)) {
            $this->selecionados = array_values(array_diff($this->selecionados, [$id]));
        } else {
            $this->selecionados[] = $id;
        }
    }

    public function selecionarTodosDaPagina(): void
    {
        $idsPagina = $this->produtos()->pluck('id')->all();

        $todosSelecionados = array_diff($idsPagina, $this->selecionados) === [];

        $this->selecionados = $todosSelecionados
            ? array_values(array_diff($this->selecionados, $idsPagina))
            : array_values(array_unique(array_merge($this->selecionados, $idsPagina)));
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function ativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Produto::whereIn('id', $this->selecionados)->update(['ativo' => true]);

        $this->limparSelecao();
        $this->dispatch('produto-updated');
    }

    public function desativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Produto::whereIn('id', $this->selecionados)->update(['ativo' => false]);

        $this->limparSelecao();
        $this->dispatch('produto-updated');
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Produto::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('produto-deleted');
    }

    #[Computed]
    public function produtoAlvo(): ?Produto
    {
        return $this->produtoParaExcluir
            ? Produto::find($this->produtoParaExcluir)
            : null;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categorias(): array
    {
        return Produto::whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Produto::query()->count(),
            'ativos' => Produto::query()->where('ativo', true)->count(),
            'inativos' => Produto::query()->where('ativo', false)->count(),
            'categorias' => Produto::query()->whereNotNull('categoria')->distinct()->count(),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Produto>
     */
    public function produtos(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryProdutos()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<Produto>
     */
    private function queryProdutos(): Builder
    {
        return Produto::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('codigo', 'like', "%{$search}%")
                        ->orWhere('categoria', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroCategoria, function ($query, $categoria) {
                $query->where('categoria', $categoria);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroCategoria', 'filtroStatus']);

        $this->sortField = 'nome';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.produtos.index', [
            'produtos' => $this->produtos(),
        ]);
    }
}
