<?php

namespace App\Livewire\Servicos;

use App\Jobs\ProcessServicoImport;
use App\Models\Servico;
use App\Models\ServicoImport;
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

#[Title('ServiÃ§os')]
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

    public ?int $servicoParaExcluir = null;

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

    public function toggleAtivo(Servico $servico): void
    {
        $servico->update(['ativo' => ! $servico->ativo]);

        $this->dispatch('servico-updated');
    }

    public function destroy(Servico $servico): void
    {
        $servico->delete();

        $this->servicoParaExcluir = null;
        $this->limparSelecao();

        $this->dispatch('servico-deleted');
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
        $idsPagina = $this->servicos()->pluck('id')->all();

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

        Servico::whereIn('id', $this->selecionados)->update(['ativo' => true]);

        $this->limparSelecao();
        $this->dispatch('servico-updated');
    }

    public function desativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Servico::whereIn('id', $this->selecionados)->update(['ativo' => false]);

        $this->limparSelecao();
        $this->dispatch('servico-updated');
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Servico::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('servico-deleted');
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
            'imports/servico',
            'local',
        );

        $import = ServicoImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => ServicoImport::STATUS_PENDENTE,
        ]);

        ProcessServicoImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o iniciada. Os serviÃ§os serÃ£o importados em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = ServicoImport::query()
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

            if ($importacao->status === ServicoImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o concluÃ­da: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === ServicoImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === ServicoImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum serviÃ§o selecionado.'));
        }

        $servicos = Servico::whereIn('id', $this->selecionados)
            ->orderBy('nome')
            ->get();

        return $exporter->download(
            'servicos-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($servicos),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryServicos();

        return $exporter->download(
            'servicos.xlsx',
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
     * @param  Collection<int, Servico>  $servicos
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $servicos): array
    {
        return $servicos->map(function (Servico $servico): array {
            return [
                $servico->nome,
                $servico->codigo,
                $servico->categoria,
                $servico->descricao,
                $servico->preco !== null ? (float) $servico->preco : null,
                $servico->ativo ? 'Ativo' : 'Inativo',
            ];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categorias(): array
    {
        return Servico::whereNotNull('categoria')
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
            'total' => Servico::query()->count(),
            'ativos' => Servico::query()->where('ativo', true)->count(),
            'inativos' => Servico::query()->where('ativo', false)->count(),
            'categorias' => Servico::query()->whereNotNull('categoria')->distinct()->count(),
        ];
    }

    #[Computed]
    public function servicoAlvo(): ?Servico
    {
        return $this->servicoParaExcluir
            ? Servico::find($this->servicoParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, Servico>
     */
    public function servicos(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryServicos()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<Servico>
     */
    private function queryServicos(): Builder
    {
        return Servico::query()
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
        return view('livewire.servicos.index', [
            'servicos' => $this->servicos(),
        ]);
    }
}
