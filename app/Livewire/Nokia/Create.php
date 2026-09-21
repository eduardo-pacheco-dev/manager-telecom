<?php

namespace App\Livewire\Nokia;

use App\Models\Estacao;
use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoHistorico;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Projeto Nokia')]
class Create extends Component
{
    public string $codigo = '';

    public string $nome = '';

    public string $descricao = '';

    public string $oc = '';

    public string $os_fam_entrega = '';

    public string $os_fam_instalacao = '';

    public string $os_fam_panoramica = '';

    public string $os_fam_desinstalacao = '';

    public string $status = 'Em andamento';

    public ?string $data_inicio = null;

    public ?string $data_fim = null;

    public bool $ativo = true;

    public ?string $estacao_id = null;

    public string $buscaEstacao = '';

    public function mount(): void
    {
        $this->codigo = $this->gerarCodigo();
    }

    public function gerarCodigo(): string
    {
        $ultimo = NokiaProjeto::query()
            ->where('codigo', 'like', 'NOK-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'NOK-'))
            ->max() ?? 0;

        return 'NOK-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:nokia_projetos,codigo'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'oc' => ['nullable', 'string', 'max:255'],
            'os_fam_entrega' => ['nullable', 'string', 'max:255'],
            'os_fam_instalacao' => ['nullable', 'string', 'max:255'],
            'os_fam_panoramica' => ['nullable', 'string', 'max:255'],
            'os_fam_desinstalacao' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(NokiaProjeto::STATUS)],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date'],
            'ativo' => ['boolean'],
            'estacao_id' => ['required', 'exists:estacoes,id'],
        ]);

        $projeto = NokiaProjeto::create($validated);

        $projeto->ensureEtapas();

        $projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_CRIACAO,
            __('Projeto criado'),
        );

        $estacao = Estacao::query()
            ->whereKey($this->estacao_id)
            ->whereNull('projeto_nokia_id')
            ->first();

        if ($estacao) {
            $estacao->update(['projeto_nokia_id' => $projeto->id]);

            $projeto->registrarHistorico(
                NokiaProjetoHistorico::TIPO_ESTACAO_VINCULADA,
                __('Estação vinculada').' '.$estacao->site_id,
            );
        }

        Flux::toast(variant: 'success', text: __('Projeto Nokia criado com sucesso.'));

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function selectEstacao(int $estacaoId): void
    {
        $this->estacao_id = (string) $estacaoId;

        $this->buscaEstacao = '';
    }

    /**
     * @return Collection<int, Estacao>
     */
    #[Computed]
    public function estacoesEncontradas(): Collection
    {
        return Estacao::query()
            ->whereNull('projeto_nokia_id')
            ->when($this->buscaEstacao !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('site_id', 'like', "%{$this->buscaEstacao}%")
                        ->orWhere('endereco_id', 'like', "%{$this->buscaEstacao}%")
                        ->orWhere('municipio', 'like', "%{$this->buscaEstacao}%");
                });
            })
            ->orderBy('site_id')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function estacaoSelecionada(): ?Estacao
    {
        return $this->estacao_id
            ? Estacao::find($this->estacao_id)
            : null;
    }

    public function render(): View
    {
        return view('livewire.nokia.create');
    }
}
