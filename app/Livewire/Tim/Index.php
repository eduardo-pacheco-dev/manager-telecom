<?php

namespace App\Livewire\Tim;

use App\Jobs\ProcessTimProjetoImport;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoImport;
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

#[Title('Projetos TIM Implantação RF')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = ['codigo', 'nome', 'status', 'data_inicio', 'data_fim', 'ativo'];

    public string $search = '';

    public string $filtroStatus = '';

    public string $sortField = 'codigo';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $projetoParaExcluir = null;

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
        $idsPagina = $this->projetos()->pluck('id')->all();

        $todosSelecionados = array_diff($idsPagina, $this->selecionados) === [];

        $this->selecionados = $todosSelecionados
            ? array_values(array_diff($this->selecionados, $idsPagina))
            : array_values(array_unique(array_merge($this->selecionados, $idsPagina)));
    }

    public function limparSelecao(): void
    {
        $this->selecionados = [];
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        TimProjeto::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('tim-projeto-deleted');
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryProjetos();

        return $exporter->download(
            'projetos-tim.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($query->orderBy('codigo')->get()),
        );
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum projeto selecionado.'));
        }

        $projetos = TimProjeto::whereIn('id', $this->selecionados)->orderBy('codigo')->get();

        return $exporter->download(
            'projetos-tim-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($projetos),
        );
    }

    /**
     * @return array<int, string>
     */
    private function cabecalhoExportacao(): array
    {
        return ['Código', 'Nome', 'Cliente', 'Descrição', 'Status', 'Data de início', 'Data de fim', 'Situação'];
    }

    /**
     * @param  Collection<int, TimProjeto>  $projetos
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $projetos): array
    {
        return $projetos->map(function (TimProjeto $projeto): array {
            return [
                $projeto->codigo,
                $projeto->nome,
                $projeto->cliente?->nome,
                $projeto->descricao,
                $projeto->status,
                $projeto->data_inicio?->format('d/m/Y'),
                $projeto->data_fim?->format('d/m/Y'),
                $projeto->ativo ? 'Ativo' : 'Inativo',
            ];
        })->all();
    }

    public function destroy(TimProjeto $projeto): void
    {
        OrdemServico::where('projeto_tim_id', $projeto->id)->update(['projeto_tim_id' => null]);

        $projeto->delete();

        $this->projetoParaExcluir = null;
        $this->limparSelecao();

        $this->dispatch('tim-projeto-deleted');
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
            'import_arquivo.max' => __('O arquivo não pode ter mais de 200 MB.'),
            'import_arquivo.mimes' => __('O arquivo deve ser um Excel (.xlsx) ou CSV.'),
        ]);

        $caminho = $this->import_arquivo->store(
            'imports/projeto-tim',
            'local',
        );

        $import = TimProjetoImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => TimProjetoImport::STATUS_PENDENTE,
        ]);

        ProcessTimProjetoImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('Importação iniciada. Os projetos serão importados em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = TimProjetoImport::query()
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

            if ($importacao->status === TimProjetoImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('Importação concluída: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === TimProjetoImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('Importação falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === TimProjetoImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('Importação em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => TimProjeto::query()->count(),
            'ativos' => TimProjeto::query()->where('ativo', true)->count(),
            'ordens' => OrdemServico::query()->whereNotNull('projeto_tim_id')->count(),
            'concluidos' => TimProjeto::query()->where('status', 'Concluído')->count(),
        ];
    }

    #[Computed]
    public function projetoAlvo(): ?TimProjeto
    {
        return $this->projetoParaExcluir
            ? TimProjeto::find($this->projetoParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, TimProjeto>
     */
    public function projetos(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryProjetos()
            ->withCount('ordensServico')
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<TimProjeto>
     */
    private function queryProjetos(): Builder
    {
        return TimProjeto::query()
            ->withCount('ordensServico')
            ->with('cliente')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('nome', 'like', "%{$search}%")
                        ->orWhere('descricao', 'like', "%{$search}%")
                        ->orWhereHas('cliente', fn ($cliente) => $cliente->where('nome', 'like', "%{$search}%"));
                });
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('status', $this->filtroStatus);
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroStatus']);

        $this->sortField = 'codigo';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.tim.index', [
            'projetos' => $this->projetos(),
        ]);
    }
}