<?php

namespace App\Livewire\Estacoes;

use App\Models\Estacao;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Nova Estação')]
class Create extends Component
{
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

    public function save(): void
    {
        $this->normalizeDecimals();

        $validated = $this->validate([
            'site_id' => ['required', 'string', 'max:255', 'unique:estacoes,site_id'],
            'tipo_elemento' => ['nullable', Rule::in(Estacao::TIPOS_ELEMENTO)],
            'tecnologia' => ['nullable', Rule::in(Estacao::TECNOLOGIAS)],
            'tipo_conexao' => ['nullable', Rule::in(Estacao::TIPOS_CONEXAO)],
            'endereco_id' => ['nullable', 'string', 'max:255'],
            'classificacao' => ['nullable', Rule::in(Estacao::CLASSIFICACOES)],
            'data_aquisicao' => ['nullable', 'date'],
            'data_construcao' => ['nullable', 'date'],
            'data_ativacao' => ['nullable', 'date'],
            'data_desativacao' => ['nullable', 'date'],
            'data_cancelamento' => ['nullable', 'date'],
            'tipo_contrato_area' => ['nullable', Rule::in(Estacao::TIPOS_CONTRATO)],
            'detentor_area' => ['nullable', Rule::in(Estacao::DETENTORES)],
            'tipo_contrato_infra' => ['nullable', Rule::in(Estacao::TIPOS_CONTRATO)],
            'detentor_infra' => ['nullable', Rule::in(Estacao::DETENTORES)],
            'tipo_infra' => ['nullable', Rule::in(Estacao::TIPOS_INFRA)],
            'tipo_ev' => ['nullable', Rule::in(Estacao::TIPOS_EV)],
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
            'status' => ['nullable', Rule::in(Estacao::STATUS)],
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
        return view('livewire.estacoes.create');
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
