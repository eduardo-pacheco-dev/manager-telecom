<?php

namespace App\Livewire\Tim;

use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\TimProjeto;
use App\Models\TimProjetoAnexo;
use App\Models\TimProjetoHistorico;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Novo Projeto TIM Implantação RF')]
class Create extends Component
{
    use WithFileUploads;

    public string $codigo = '';

    public string $nome = '';

    public string $descricao = '';

    public string $oc = '';

    public string $os_fam_entrega = '';

    public string $os_fam_instalacao = '';

    public string $os_fam_panoramica = '';

    public string $os_fam_desinstalacao = '';

    public string $status = 'Planejamento';

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

    public ?string $ordem_servico_id = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $anexos_tssr = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $anexos_docd = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $anexos_notas_fiscais = [];

    public function mount(): void
    {
        $this->codigo = $this->gerarCodigo();
    }

    public function gerarCodigo(): string
    {
        $ultimo = TimProjeto::query()
            ->where('codigo', 'like', 'TIM-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'TIM-'))
            ->max() ?? 0;

        return 'TIM-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function gerarCodigoOrdem(): string
    {
        $ultimo = OrdemServico::query()
            ->where('codigo', 'like', 'OS-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'OS-'))
            ->max() ?? 0;

        return 'OS-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $this->data_inicio = $this->data_inicio ?: $this->real_mos;
        $this->data_fim = $this->data_fim ?: $this->real_rfa;

        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:tim_projetos,codigo'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'oc' => ['nullable', 'string', 'max:255'],
            'os_fam_entrega' => ['nullable', 'string', 'max:255'],
            'os_fam_instalacao' => ['nullable', 'string', 'max:255'],
            'os_fam_panoramica' => ['nullable', 'string', 'max:255'],
            'os_fam_desinstalacao' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(TimProjeto::STATUS)],
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
            'ordem_servico_id' => ['nullable', 'exists:ordens_servico,id'],
            'anexos_tssr' => ['nullable', 'array'],
            'anexos_tssr.*' => ['file', 'max:20480'],
            'anexos_docd' => ['nullable', 'array'],
            'anexos_docd.*' => ['file', 'max:20480'],
            'anexos_notas_fiscais' => ['nullable', 'array'],
            'anexos_notas_fiscais.*' => ['file', 'max:20480'],
        ]);

        $projeto = TimProjeto::create($validated);

        $projeto->ensureEtapas();

        $this->salvarAnexos($projeto, TimProjetoAnexo::CATEGORIAS[0], $this->anexos_tssr);
        $this->salvarAnexos($projeto, TimProjetoAnexo::CATEGORIAS[1], $this->anexos_docd);
        $this->salvarAnexos($projeto, TimProjetoAnexo::CATEGORIAS[2], $this->anexos_notas_fiscais);

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
            TimProjetoHistorico::TIPO_CRIACAO,
            __('Projeto criado'),
        );

        $estacao = Estacao::query()
            ->whereKey($this->estacao_id)
            ->whereNull('projeto_tim_id')
            ->first();

        if ($estacao) {
            $estacao->update(['projeto_tim_id' => $projeto->id]);

            $projeto->registrarHistorico(
                TimProjetoHistorico::TIPO_ESTACAO_VINCULADA,
                __('Estação vinculada').' '.$estacao->site_id,
            );
        }

        if ($this->ordem_servico_id) {
            $ordem = OrdemServico::query()
                ->whereKey($this->ordem_servico_id)
                ->whereNull('projeto_tim_id')
                ->first();
        } else {
            $ordem = OrdemServico::create([
                'codigo' => $this->gerarCodigoOrdem(),
                'titulo' => __('Serviço do projeto').' '.$projeto->codigo,
                'tipo' => 'Instalação',
                'escopo' => 'Estação',
                'status' => 'Aberta',
                'prioridade' => 'Média',
                'estacao_a_id' => $this->estacao_id,
                'data_abertura' => now()->toDateString(),
                'projeto_tim_id' => $projeto->id,
            ]);
        }

        if ($ordem) {
            $ordem->update(['projeto_tim_id' => $projeto->id]);

            $projeto->registrarHistorico(
                TimProjetoHistorico::TIPO_OS_VINCULADA,
                __('OS vinculada').' '.$ordem->codigo,
            );
        }

        Flux::toast(variant: 'success', text: __('Projeto TIM Implantação RF criado com sucesso.'));

        $this->redirect(route('tim.index'), navigate: true);
    }

    public function selectEstacao(int $estacaoId): void
    {
        $this->estacao_id = (string) $estacaoId;

        $this->buscaEstacao = '';
    }

    public function anexoUrl(string $filename, bool $download = false): string
    {
        $route = $download ? 'tim.anexos-tmp.download' : 'tim.anexos-tmp.preview';

        return URL::temporarySignedRoute($route, now()->addMinutes(30), ['filename' => $filename]);
    }

    public function removerAnexoTemporario(string $categoria, string $filename): void
    {
        $this->{$categoria} = array_values(array_filter(
            $this->{$categoria},
            fn (TemporaryUploadedFile $arquivo): bool => $arquivo->getFilename() !== $filename,
        ));
    }

    /**
     * @param  array<int, TemporaryUploadedFile>  $arquivos
     */
    private function salvarAnexos(TimProjeto $projeto, string $categoria, array $arquivos): void
    {
        foreach ($arquivos as $arquivo) {
            $caminho = $arquivo->storeAs(
                'anexos/projeto/'.$projeto->id,
                Str::uuid().'.'.$arquivo->getClientOriginalExtension(),
                'local',
            );

            $projeto->anexos()->create([
                'categoria' => $categoria,
                'nome' => $arquivo->getClientOriginalName(),
                'arquivo' => $caminho,
                'mime' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }
    }

    /**
     * @return Collection<int, Estacao>
     */
    #[Computed]
    public function estacoesEncontradas(): Collection
    {
        return Estacao::query()
            ->whereNull('projeto_tim_id')
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

    /**
     * @return Collection<int, OrdemServico>
     */
    #[Computed]
    public function ordensDisponiveis(): Collection
    {
        return OrdemServico::query()
            ->whereNull('projeto_tim_id')
            ->orderBy('codigo')
            ->limit(50)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.tim.create');
    }
}