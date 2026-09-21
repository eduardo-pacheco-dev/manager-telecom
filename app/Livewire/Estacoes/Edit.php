<?php

namespace App\Livewire\Estacoes;

use App\Models\Estacao;
use App\Models\EstacaoDetentor;
use App\Models\EstacaoEndereco;
use App\Models\EstacaoStation;
use App\Models\EstacaoStatus;
use App\Models\EstacaoTecnologia;
use App\Models\EstacaoTipoConexao;
use App\Models\EstacaoTipoEv;
use App\Models\EstacaoTipoInfra;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Estação')]
class Edit extends Component
{
    public ?Estacao $estacao = null;

    public string $site_id = '';

    public string $tipo_elemento = '';

    public string $tecnologia = '';

    public string $tipo_conexao = '';

    public string $endereco_id = '';

    public string $classificacao = '';

    public ?string $data_aquisicao = null;

    public ?string $data_construcao = null;

    public ?string $data_ativacao = null;

    public ?string $data_desativacao = null;

    public ?string $data_cancelamento = null;

    public string $tipo_contrato_area = '';

    public string $detentor_area = '';

    public string $tipo_contrato_infra = '';

    public string $detentor_infra = '';

    public string $tipo_infra = '';

    public string $tipo_ev = '';

    public string $fornecedor_ev = '';

    public string $observacao = '';

    public string $justificativa = '';

    public string $tipo_logradouro = '';

    public string $logradouro = '';

    public string $numero = '';

    public string $complemento = '';

    public string $bairro = '';

    public string $municipio = '';

    public string $estado = '';

    public string $cep = '';

    public string $regional = '';

    public ?string $latitude = null;

    public ?string $longitude = null;

    public string $status = '';

    public string $tipo_torre = '';

    public ?string $aev_nominal = null;

    public ?string $area_solo = null;

    public ?string $altura_estrutura = null;

    public string $station_id = '';

    public string $ordem_complexa = '';

    public string $observacao_thq = '';

    public string $situacao = '';

    public string $ots = '';

    public function mount(Estacao $estacao): void
    {
        $this->estacao = $estacao;
        $this->site_id = $estacao->site_id;
        $this->tipo_elemento = $estacao->tipo_elemento ?? '';
        $this->tecnologia = $estacao->tecnologia ?? '';
        $this->tipo_conexao = $estacao->tipo_conexao ?? '';
        $this->endereco_id = $estacao->endereco_id ?? '';
        $this->classificacao = $estacao->classificacao ?? '';
        $this->data_aquisicao = $estacao->data_aquisicao?->format('Y-m-d');
        $this->data_construcao = $estacao->data_construcao?->format('Y-m-d');
        $this->data_ativacao = $estacao->data_ativacao?->format('Y-m-d');
        $this->data_desativacao = $estacao->data_desativacao?->format('Y-m-d');
        $this->data_cancelamento = $estacao->data_cancelamento?->format('Y-m-d');
        $this->tipo_contrato_area = $estacao->tipo_contrato_area ?? '';
        $this->detentor_area = $estacao->detentor_area ?? '';
        $this->tipo_contrato_infra = $estacao->tipo_contrato_infra ?? '';
        $this->detentor_infra = $estacao->detentor_infra ?? '';
        $this->tipo_infra = $estacao->tipo_infra ?? '';
        $this->tipo_ev = $estacao->tipo_ev ?? '';
        $this->fornecedor_ev = $estacao->fornecedor_ev ?? '';
        $this->observacao = $estacao->observacao ?? '';
        $this->justificativa = $estacao->justificativa ?? '';
        $this->tipo_logradouro = $estacao->tipo_logradouro ?? '';
        $this->logradouro = $estacao->logradouro ?? '';
        $this->numero = $estacao->numero ?? '';
        $this->complemento = $estacao->complemento ?? '';
        $this->bairro = $estacao->bairro ?? '';
        $this->municipio = $estacao->municipio ?? '';
        $this->estado = $estacao->estado ?? '';
        $this->cep = $estacao->cep ?? '';
        $this->regional = $estacao->regional ?? '';
        $this->latitude = $estacao->latitude !== null ? (string) $estacao->latitude : null;
        $this->longitude = $estacao->longitude !== null ? (string) $estacao->longitude : null;
        $this->status = $estacao->status ?? '';
        $this->tipo_torre = $estacao->tipo_torre ?? '';
        $this->aev_nominal = $estacao->aev_nominal !== null ? (string) $estacao->aev_nominal : null;
        $this->area_solo = $estacao->area_solo !== null ? (string) $estacao->area_solo : null;
        $this->altura_estrutura = $estacao->altura_estrutura !== null ? (string) $estacao->altura_estrutura : null;
        $this->station_id = $estacao->station_id ?? '';
        $this->ordem_complexa = $estacao->ordem_complexa ?? '';
        $this->observacao_thq = $estacao->observacao_thq ?? '';
        $this->situacao = $estacao->situacao ?? '';
        $this->ots = $estacao->ots ?? '';
    }

    public function save(): void
    {
        $this->normalizeDecimals();

        $validated = $this->validate([
            'site_id' => ['required', 'string', 'max:255', 'unique:estacoes,site_id,'.$this->estacao->id],
            'tipo_elemento' => ['nullable', Rule::in(Estacao::TIPOS_ELEMENTO)],
            'tecnologia' => $this->regraConfiguravel('tecnologias', 'tecnologia'),
            'tipo_conexao' => $this->regraConfiguravel('tiposConexao', 'tipo_conexao'),
            'endereco_id' => ['nullable', 'string', 'max:255'],
            'classificacao' => ['nullable', Rule::in(Estacao::CLASSIFICACOES)],
            'data_aquisicao' => ['nullable', 'date'],
            'data_construcao' => ['nullable', 'date'],
            'data_ativacao' => ['nullable', 'date'],
            'data_desativacao' => ['nullable', 'date'],
            'data_cancelamento' => ['nullable', 'date'],
            'tipo_contrato_area' => ['nullable', Rule::in(Estacao::TIPOS_CONTRATO)],
            'detentor_area' => $this->regraConfiguravel('detentores', 'detentor_area'),
            'tipo_contrato_infra' => ['nullable', Rule::in(Estacao::TIPOS_CONTRATO)],
            'detentor_infra' => ['nullable', Rule::in(Estacao::DETENTORES)],
            'tipo_infra' => $this->regraConfiguravel('tiposInfra', 'tipo_infra'),
            'tipo_ev' => $this->regraConfiguravel('tiposEv', 'tipo_ev'),
            'fornecedor_ev' => ['nullable', Rule::in(Estacao::FORNECEDORES_EV)],
            'observacao' => ['nullable', 'string', 'max:1000'],
            'justificativa' => ['nullable', 'string', 'max:1000'],
            'tipo_logradouro' => ['nullable', Rule::in(Estacao::TIPOS_LOGRADOURO)],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'municipio' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-?\d{3}$/'],
            'regional' => ['nullable', Rule::in(Estacao::REGIONAIS)],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => $this->regraConfiguravel('statuses', 'status'),
            'tipo_torre' => ['nullable', 'string', 'max:255'],
            'aev_nominal' => ['nullable', 'numeric', 'min:0'],
            'area_solo' => ['nullable', 'numeric', 'min:0'],
            'altura_estrutura' => ['nullable', 'numeric', 'min:0'],
            'station_id' => ['nullable', 'string', 'max:255'],
            'ordem_complexa' => ['nullable', 'string', 'max:255'],
            'observacao_thq' => ['nullable', 'string', 'max:1000'],
            'situacao' => ['nullable', Rule::in(Estacao::SITUACOES)],
            'ots' => ['nullable', 'string', 'max:255'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        $this->estacao->update($validated);

        Flux::toast(variant: 'success', text: __('Estação atualizada com sucesso.'));

        $this->redirect(route('estacoes.index'), navigate: true);
    }

    public function updatedEstado(string $value): void
    {
        $this->estado = mb_strtoupper($value);
    }

    public function render(): View
    {
        return view('livewire.estacoes.edit', [
            'tecnologias' => $this->tecnologias(),
            'tiposConexao' => $this->tiposConexao(),
            'enderecos' => $this->enderecos(),
            'stations' => $this->stations(),
            'statuses' => $this->statuses(),
            'detentores' => $this->detentores(),
            'tiposInfra' => $this->tiposInfra(),
            'tiposEv' => $this->tiposEv(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tecnologias(): array
    {
        $valores = EstacaoTecnologia::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::TECNOLOGIAS;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tiposConexao(): array
    {
        $valores = EstacaoTipoConexao::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::TIPOS_CONEXAO;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function enderecos(): array
    {
        return EstacaoEndereco::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function stations(): array
    {
        return EstacaoStation::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function statuses(): array
    {
        $valores = EstacaoStatus::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::STATUS;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function detentores(): array
    {
        $valores = EstacaoDetentor::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::DETENTORES;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tiposInfra(): array
    {
        $valores = EstacaoTipoInfra::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::TIPOS_INFRA;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function tiposEv(): array
    {
        $valores = EstacaoTipoEv::query()->where('ativo', true)->orderBy('nome')->pluck('nome')->all();

        return $valores !== [] ? $valores : Estacao::TIPOS_EV;
    }

    private function normalizeDecimals(): void
    {
        foreach (['latitude', 'longitude', 'aev_nominal', 'area_solo', 'altura_estrutura'] as $campo) {
            if (is_string($this->{$campo}) && str_contains($this->{$campo}, ',')) {
                $this->{$campo} = str_replace(',', '.', $this->{$campo});
            }
        }
    }

    /**
     * @return array<int, mixed>
     */
    private function regraConfiguravel(string $metodo, string $campo): array
    {
        $regra = ['nullable', 'string', 'max:255'];

        $opcoes = $this->{$metodo}();

        if ($this->estacao->{$campo}) {
            $opcoes[] = $this->estacao->{$campo};
        }

        $regra[] = Rule::in(array_values(array_unique($opcoes)));

        return $regra;
    }
}
