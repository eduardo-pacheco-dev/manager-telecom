<?php

namespace App\Livewire\Clientes;

use App\Jobs\ProcessClienteImport;
use App\Models\Cliente;
use App\Models\ClienteImport;
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

#[Title('Clientes')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = [
        'nome', 'email', 'documento', 'segmento', 'ativo',
    ];

    public string $search = '';

    public string $filtroSegmento = '';

    public string $filtroStatus = '';

    public string $sortField = 'nome';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public ?int $clienteParaExcluir = null;

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

    public function updatingFiltroSegmento(): void
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

    public function toggleAtivo(Cliente $cliente): void
    {
        $cliente->update(['ativo' => ! $cliente->ativo]);

        $this->dispatch('cliente-updated');
    }

    public function destroy(Cliente $cliente): void
    {
        $cliente->delete();

        $this->clienteParaExcluir = null;

        $this->dispatch('cliente-deleted');
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
        $idsPagina = $this->clientes()->pluck('id')->all();

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

        Cliente::whereIn('id', $this->selecionados)->update(['ativo' => true]);

        $this->limparSelecao();
        $this->dispatch('cliente-updated');
    }

    public function desativarSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Cliente::whereIn('id', $this->selecionados)->update(['ativo' => false]);

        $this->limparSelecao();
        $this->dispatch('cliente-updated');
    }

    public function excluirSelecionados(): void
    {
        if ($this->selecionados === []) {
            return;
        }

        Cliente::whereIn('id', $this->selecionados)->delete();

        $this->limparSelecao();
        $this->dispatch('cliente-deleted');
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
            'imports/cliente',
            'local',
        );

        $import = ClienteImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => ClienteImport::STATUS_PENDENTE,
        ]);

        ProcessClienteImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o iniciada. Os clientes serÃ£o importados em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = ClienteImport::query()
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

            if ($importacao->status === ClienteImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o concluÃ­da: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === ClienteImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === ClienteImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('ImportaÃ§Ã£o em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    public function exportarSelecionados(ExcelExporter $exporter): StreamedResponse
    {
        if ($this->selecionados === []) {
            abort(422, __('Nenhum cliente selecionado.'));
        }

        $clientes = Cliente::whereIn('id', $this->selecionados)
            ->orderBy('nome')
            ->get();

        return $exporter->download(
            'clientes-selecionados.xlsx',
            $this->cabecalhoExportacao(),
            $this->linhasExportacao($clientes),
        );
    }

    public function exportarTodos(ExcelExporter $exporter): StreamedResponse
    {
        $query = $this->queryClientes();

        return $exporter->download(
            'clientes.xlsx',
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
            'Nome', 'E-mail', 'CNPJ', 'Telefone', 'Segmento', 'Cidade', 'Estado', 'Status',
        ];
    }

    /**
     * @param  Collection<int, Cliente>  $clientes
     * @return array<int, array<int, mixed>>
     */
    private function linhasExportacao(Collection $clientes): array
    {
        return $clientes->map(function (Cliente $cliente): array {
            return [
                $cliente->nome,
                $cliente->email,
                $cliente->documento,
                $cliente->telefone,
                $cliente->segmento,
                $cliente->cidade,
                $cliente->estado,
                $cliente->ativo ? 'Ativo' : 'Inativo',
            ];
        })->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function segmentos(): array
    {
        return Cliente::SEGMENTOS;
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Cliente::query()->count(),
            'ativos' => Cliente::query()->where('ativo', true)->count(),
            'inativos' => Cliente::query()->where('ativo', false)->count(),
            'segmentos' => Cliente::query()->whereNotNull('segmento')->distinct()->count(),
        ];
    }

    #[Computed]
    public function clienteAlvo(): ?Cliente
    {
        return $this->clienteParaExcluir
            ? Cliente::find($this->clienteParaExcluir)
            : null;
    }

    /**
     * @return LengthAwarePaginator<int, Cliente>
     */
    public function clientes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryClientes()
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    /**
     * @return Builder<Cliente>
     */
    private function queryClientes(): Builder
    {
        return Cliente::query()
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('documento', 'like', "%{$search}%")
                        ->orWhere('cidade', 'like', "%{$search}%")
                        ->orWhere('telefone', 'like', "%{$search}%");
                });
            })
            ->when($this->filtroSegmento, function ($query, $segmento) {
                $query->where('segmento', $segmento);
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('ativo', $this->filtroStatus === 'ativo');
            });
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroSegmento', 'filtroStatus']);

        $this->sortField = 'nome';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.clientes.index', [
            'clientes' => $this->clientes(),
        ]);
    }
}
