<?php

namespace App\Livewire\OrdensServico;

use App\Jobs\ProcessOrdemServicoImport;
use App\Models\OrdemServico;
use App\Models\OrdemServicoImport;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Ordens de Serviço')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    private const SORTABLE = [
        'codigo', 'titulo', 'tipo', 'status', 'prioridade', 'radio_link_id', 'data_abertura',
    ];

    public string $search = '';

    public string $filtroStatus = '';

    public string $filtroTipo = '';

    public string $filtroPrioridade = '';

    public string $sortField = 'codigo';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public bool $showImportModal = false;

    public $import_arquivo = null;

    /** @var array<int, string> */
    public array $importesStatus = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroPrioridade(): void
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

    public function destroy(OrdemServico $ordemServico): void
    {
        $ordemServico->delete();

        $this->dispatch('ordem-servico-deleted');
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
            'imports/ordem-servico',
            'local',
        );

        $import = OrdemServicoImport::create([
            'user_id' => auth()->id(),
            'arquivo' => $caminho,
            'nome_original' => $this->import_arquivo->getClientOriginalName(),
            'status' => OrdemServicoImport::STATUS_PENDENTE,
        ]);

        ProcessOrdemServicoImport::dispatch($import->id);

        $this->reset('import_arquivo', 'showImportModal');

        $this->dispatch('flux-toast', text: __('Importação iniciada. As ordens serão importadas em segundo plano.'), variant: 'success');
    }

    public function verificarImportacoes(): void
    {
        $importacoes = OrdemServicoImport::query()
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

            if ($importacao->status === OrdemServicoImport::STATUS_CONCLUIDO) {
                $this->dispatch('flux-toast', text: __('Importação concluída: ').$importacao->nome_original, variant: 'success');
            } elseif ($importacao->status === OrdemServicoImport::STATUS_FALHOU) {
                $this->dispatch('flux-toast', text: __('Importação falhou: ').$importacao->nome_original, variant: 'danger');
            } elseif ($importacao->status === OrdemServicoImport::STATUS_PROCESSANDO) {
                $this->dispatch('flux-toast', text: __('Importação em andamento: ').$importacao->nome_original, variant: 'info');
            }
        }
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return OrdemServico::whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tipos(): array
    {
        return OrdemServico::whereNotNull('tipo')
            ->distinct()
            ->pluck('tipo')
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
            'total' => OrdemServico::query()->count(),
            'abertas' => OrdemServico::query()->whereIn('status', ['Aberta', 'Em andamento', 'Aguardando'])->count(),
            'concluidas' => OrdemServico::query()->where('status', 'Concluída')->count(),
            'urgentes' => OrdemServico::query()->where('prioridade', 'Urgente')->where('status', '!=', 'Concluída')->count(),
        ];
    }

    /**
     * @return LengthAwarePaginator<int, OrdemServico>
     */
    public function ordensServico(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return OrdemServico::query()
            ->with(['radioLink', 'responsavel'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                        ->orWhere('titulo', 'like', "%{$search}%")
                        ->orWhere('solicitante', 'like', "%{$search}%")
                        ->orWhere('supervisor', 'like', "%{$search}%")
                        ->orWhere('projeto', 'like', "%{$search}%")
                        ->orWhereHas('radioLink', fn ($q) => $q->where('codigo', 'like', "%{$search}%"));
                });
            })
            ->when($this->filtroStatus !== '', function ($query) {
                $query->where('status', $this->filtroStatus);
            })
            ->when($this->filtroTipo !== '', function ($query) {
                $query->where('tipo', $this->filtroTipo);
            })
            ->when($this->filtroPrioridade !== '', function ($query) {
                $query->where('prioridade', $this->filtroPrioridade);
            })
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filtroStatus', 'filtroTipo', 'filtroPrioridade']);

        $this->sortField = 'codigo';
        $this->sortDirection = 'asc';

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.ordens-servico.index', [
            'ordensServico' => $this->ordensServico(),
        ]);
    }
}
