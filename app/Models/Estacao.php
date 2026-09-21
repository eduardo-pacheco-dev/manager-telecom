<?php

namespace App\Models;

use Database\Factories\EstacaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $site_id
 * @property string|null $tipo_elemento
 * @property string|null $tecnologia
 * @property string|null $tipo_conexao
 * @property string|null $operadora
 * @property string|null $endereco_id
 * @property string|null $classificacao
 * @property Carbon|null $data_aquisicao
 * @property Carbon|null $data_construcao
 * @property Carbon|null $data_ativacao
 * @property Carbon|null $data_desativacao
 * @property Carbon|null $data_cancelamento
 * @property string|null $tipo_contrato_area
 * @property string|null $detentor_area
 * @property string|null $tipo_contrato_infra
 * @property string|null $detentor_infra
 * @property string|null $tipo_infra
 * @property string|null $tipo_ev
 * @property string|null $fornecedor_ev
 * @property string|null $observacao
 * @property string|null $justificativa
 * @property string|null $tipo_logradouro
 * @property string|null $logradouro
 * @property string|null $numero
 * @property string|null $complemento
 * @property string|null $bairro
 * @property string|null $municipio
 * @property string|null $estado
 * @property string|null $cep
 * @property string|null $regional
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $status
 * @property string|null $tipo_torre
 * @property string|null $aev_nominal
 * @property string|null $area_solo
 * @property string|null $altura_estrutura
 * @property string|null $station_id
 * @property string|null $ordem_complexa
 * @property string|null $observacao_thq
 * @property string|null $situacao
 * @property string|null $ots
 * @property int|null $projeto_nokia_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property NokiaProjeto|null $projetoNokia
 * @property Collection<int, EstacaoAnexo> $anexos
 * @property Collection<int, EstacaoComentario> $comentarios
 * @property Collection<int, RadioLink> $radioLinksA
 * @property Collection<int, RadioLink> $radioLinksB
 * @property Collection<int, OrdemServico> $ordensServicoA
 * @property Collection<int, OrdemServico> $ordensServicoB
 */
#[Fillable([
    'site_id', 'tipo_elemento', 'tecnologia', 'tipo_conexao', 'operadora', 'endereco_id',
    'classificacao', 'data_aquisicao', 'data_construcao', 'data_ativacao',
    'data_desativacao', 'data_cancelamento', 'tipo_contrato_area',
    'detentor_area', 'tipo_contrato_infra', 'detentor_infra', 'tipo_infra',
    'tipo_ev', 'fornecedor_ev', 'observacao', 'justificativa',
    'tipo_logradouro', 'logradouro', 'numero', 'complemento', 'bairro',
    'municipio', 'estado', 'cep', 'regional', 'latitude', 'longitude',
    'status', 'tipo_torre', 'aev_nominal', 'area_solo', 'altura_estrutura',
    'station_id', 'ordem_complexa', 'observacao_thq', 'situacao', 'ots',
    'projeto_nokia_id',
])]
class Estacao extends Model
{
    public const TIPOS_ELEMENTO = ['BTS', 'NODE B', 'ENODE B', 'GNODE B', 'MIMO'];

    public const TECNOLOGIAS = ['GSM', 'UMTS', 'LTE', '5G NR'];

    public const TIPOS_CONEXAO = ['Indefinido', 'Interconexão', 'Fibra Óptica', 'Microwave', 'Satélite'];

    public const CLASSIFICACOES = ['ACESSO', 'RANSHARING', 'BACKHAUL', 'TRANSPORTE'];

    public const TIPOS_CONTRATO = ['Built-to-Suit', 'Compartilhado', 'Locação', 'Comodato', 'Direito de Passagem'];

    public const DETENTORES = ['IHS BRAZIL', 'AMERICAN TOWER', 'SKY TOWERS', 'CELL SITE SOLUTIONS', 'HOMELOG'];

    public const TIPOS_INFRA = ['Greenfield', 'Rooftop', 'Solo', 'Telhado', 'Outros'];

    public const TIPOS_EV = ['TORRE METALICA TRIANGULAR', 'TORRE METALICA QUADRANGULAR', 'TORRE ESTAIADA', 'POSTE', 'ROOFTOP'];

    public const FORNECEDORES_EV = ['BRASILSAT', 'ABRITEL', 'BROADWAVE', 'HIGH TOWER', 'PADRAO'];

    public const STATUS = ['CANDIDATO A', 'Aquisitado', 'Adquirido', 'Em construção', 'Ativo', 'Inativo', 'Desativado', 'Cancelado'];

    public const TIPOS_LOGRADOURO = ['RUA', 'AVENIDA', 'ALAMEDA', 'TRAVESSA', 'RODOVIA'];

    public const REGIONAIS = ['TCO', 'TCL', 'TCN', 'TCS', 'TCT', 'TCM'];

    public const SITUACOES = ['Em operação', 'Em implantação', 'Em construção', 'VENDIDO GENESIS - 2º CLOSING', 'Site Removido', 'Cancelado'];

    /** @use HasFactory<EstacaoFactory> */
    use HasFactory;

    protected $table = 'estacoes';

    protected function casts(): array
    {
        return [
            'data_aquisicao' => 'date',
            'data_construcao' => 'date',
            'data_ativacao' => 'date',
            'data_desativacao' => 'date',
            'data_cancelamento' => 'date',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'aev_nominal' => 'decimal:2',
            'area_solo' => 'decimal:2',
            'altura_estrutura' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<NokiaProjeto, $this>
     */
    public function projetoNokia(): BelongsTo
    {
        return $this->belongsTo(NokiaProjeto::class, 'projeto_nokia_id');
    }

    /**
     * @return HasMany<EstacaoAnexo, $this>
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(EstacaoAnexo::class)->orderByDesc('created_at');
    }

    /**
     * @return HasMany<RadioLink, $this>
     */
    public function radioLinksA(): HasMany
    {
        return $this->hasMany(RadioLink::class, 'estacao_a_id');
    }

    /**
     * @return HasMany<RadioLink, $this>
     */
    public function radioLinksB(): HasMany
    {
        return $this->hasMany(RadioLink::class, 'estacao_b_id');
    }

    /**
     * @return HasMany<OrdemServico, $this>
     */
    public function ordensServicoA(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'estacao_a_id');
    }

    /**
     * @return HasMany<OrdemServico, $this>
     */
    public function ordensServicoB(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'estacao_b_id');
    }

    /**
     * @return Collection<int, RadioLink>
     */
    public function radioLinksRelacionados(): Collection
    {
        return $this->radioLinksA->merge($this->radioLinksB)->unique('id')->values();
    }

    /**
     * @return Collection<int, OrdemServico>
     */
    public function ordensServicoRelacionadas(): Collection
    {
        return $this->ordensServicoA->merge($this->ordensServicoB)->unique('id')->values();
    }

    /**
     * @return HasMany<EstacaoComentario, $this>
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(EstacaoComentario::class)->orderBy('created_at');
    }
}
