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

    public ?string $baseline_mos = null;

    public ?string $planejada_mos = null;

    public ?string $real_mos = null;

    public ?string $baseline_instalacao = null;

    public ?string $planejada_instalacao = null;

    public ?string $real_instalacao = null;

    public ?string $baseline_integracao = null;

    public ?string $planejada_integracao = null;

    public ?string $real_integracao = null;

    public ?string $baseline_rfa = null;

    public ?string $planejada_rfa = null;

    public ?string $real_rfa = null;

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
        $this->data_inicio = $this->data_inicio ?: $this->real_mos;
        $this->data_fim = $this->data_fim ?: $this->real_rfa;

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
            'baseline_mos' => ['nullable', 'date'],
            'planejada_mos' => ['nullable', 'date'],
            'real_mos' => ['nullable', 'date'],
            'baseline_instalacao' => ['nullable', 'date'],
            'planejada_instalacao' => ['nullable', 'date'],
            'real_instalacao' => ['nullable', 'date'],
            'baseline_integracao' => ['nullable', 'date'],
            'planejada_integracao' => ['nullable', 'date'],
            'real_integracao' => ['nullable', 'date'],
            'baseline_rfa' => ['nullable', 'date'],
            'planejada_rfa' => ['nullable', 'date'],
            'real_rfa' => ['nullable', 'date'],
            'ativo' => ['boolean'],
            'estacao_id' => ['required', 'exists:estacoes,id'],
        ]);

        $projeto = NokiaProjeto::create($validated);

        $projeto->ensureEtapas();

        $projeto->atualizarCronogramaEtapas([
            'MOS' => [
                'baseline' => $this->baseline_mos,
                'planejada' => $this->planejada_mos,
                'real' => $this->real_mos,
            ],
            'Instalação' => [
                'baseline' => $this->baseline_instalacao,
                'planejada' => $this->planejada_instalacao,
                'real' => $this->real_instalacao,
            ],
            'Integração' => [
                'baseline' => $this->baseline_integracao,
                'planejada' => $this->planejada_integracao,
                'real' => $this->real_integracao,
            ],
            'RFA' => [
                'baseline' => $this->baseline_rfa,
                'planejada' => $this->planejada_rfa,
                'real' => $this->real_rfa,
            ],
        ]);

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
