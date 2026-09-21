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

#[Title('Nova Estação')]
class Create extends Component
{
    public string $site_id = '';

    public string $tecnologia = '';

    public string $tipo_conexao = '';

    public string $endereco_id = '';

    public string $detentor_area = '';

    public string $tipo_infra = '';

    public string $tipo_ev = '';

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

    public function save(): void
    {
        $this->normalizeDecimals();

        $validated = $this->validate([
            'site_id' => ['required', 'string', 'max:255', 'unique:estacoes,site_id'],
            'tecnologia' => ['nullable', 'string', 'max:255', Rule::in($this->tecnologias())],
            'tipo_conexao' => ['nullable', 'string', 'max:255', Rule::in($this->tiposConexao())],
            'endereco_id' => ['nullable', 'string', 'max:255', Rule::in($this->enderecos())],
            'detentor_area' => ['nullable', 'string', 'max:255', Rule::in($this->detentores())],
            'tipo_infra' => ['nullable', 'string', 'max:255', Rule::in($this->tiposInfra())],
            'tipo_ev' => ['nullable', 'string', 'max:255', Rule::in($this->tiposEv())],
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
            'status' => ['nullable', 'string', 'max:255', Rule::in($this->statuses())],
            'tipo_torre' => ['nullable', 'string', 'max:255'],
            'aev_nominal' => ['nullable', 'numeric', 'min:0'],
            'area_solo' => ['nullable', 'numeric', 'min:0'],
            'altura_estrutura' => ['nullable', 'numeric', 'min:0'],
            'station_id' => ['nullable', 'string', 'max:255', Rule::in($this->stations())],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        Estacao::create($validated);

        Flux::toast(variant: 'success', text: __('Estação criada com sucesso.'));

        $this->redirect(route('estacoes.index'), navigate: true);
    }

    public function updatedEstado(string $value): void
    {
        $this->estado = mb_strtoupper($value);
    }

    public function render(): View
    {
        return view('livewire.estacoes.create', [
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
}
