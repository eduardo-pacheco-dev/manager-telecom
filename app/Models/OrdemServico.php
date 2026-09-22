<?php

namespace App\Models;

use Database\Factories\OrdemServicoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $codigo
 * @property string|null $codigo_personalizado
 * @property string|null $codigo_cliente
 * @property int|null $cliente_id
 * @property string|null $ordem_complexa
 * @property string $titulo
 * @property string|null $tipo
 * @property string|null $escopo
 * @property string|null $status
 * @property string|null $prioridade
 * @property int|null $radio_link_id
 * @property int|null $estacao_a_id
 * @property int|null $estacao_b_id
 * @property string|null $solicitante
 * @property int|null $responsavel_id
 * @property string|null $descricao
 * @property Carbon|null $data_abertura
 * @property Carbon|null $data_agendamento
 * @property Carbon|null $data_conclusao
 * @property string|null $projeto
 * @property string|null $end_id_a
 * @property string|null $end_id_b
 * @property string|null $supervisor
 * @property string|null $coordenador
 * @property string|null $oc_tim
 * @property string|null $chave_mw
 * @property string|null $smp_nokia
 * @property string|null $observacao
 * @property array<string, mixed>|null $dados_brutos
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Cliente|null $cliente
 * @property TimProjeto|null $projetoTim
 * @property RadioLink|null $radioLink
 * @property Estacao|null $estacaoA
 * @property Estacao|null $estacaoB
 * @property User|null $responsavel
 * @property Collection<int, OrdemServicoAnexo> $anexos
 * @property Collection<int, OrdemServicoComentario> $comentarios
 */
#[Fillable([
    'codigo', 'codigo_personalizado', 'codigo_cliente', 'cliente_id', 'ordem_complexa',
    'titulo', 'tipo', 'escopo', 'status', 'prioridade', 'radio_link_id',
    'estacao_a_id', 'estacao_b_id', 'solicitante', 'responsavel_id',
    'descricao', 'data_abertura', 'data_agendamento', 'data_conclusao',
    'projeto', 'end_id_a', 'end_id_b', 'supervisor', 'coordenador',
    'oc_tim', 'chave_mw', 'smp_nokia', 'observacao', 'dados_brutos',
    'projeto_tim_id',
])]
class OrdemServico extends Model
{
    public const TIPOS = ['Manutenção', 'Instalação', 'Ativação', 'Desativação', 'Remoção', 'Inspeção', 'Corretiva', 'Preventiva', 'Outro'];

    public const STATUS = ['Aberta', 'Em andamento', 'Aguardando', 'Concluída', 'Cancelada'];

    public const PRIORIDADES = ['Baixa', 'Média', 'Alta', 'Urgente'];

    public const ESCOPOS = ['Enlace', 'Estação', 'Outro'];

    /**
     * @return array<int, string>
     */
    public static function tiposDisponiveis(): array
    {
        $tipos = OrdemServicoTipo::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return $tipos !== [] ? $tipos : self::TIPOS;
    }

    /** @use HasFactory<OrdemServicoFactory> */
    use HasFactory;

    protected $table = 'ordens_servico';

    protected function casts(): array
    {
        return [
            'data_abertura' => 'date',
            'data_agendamento' => 'date',
            'data_conclusao' => 'date',
            'dados_brutos' => 'array',
        ];
    }

    /**
     * @return BelongsTo<TimProjeto, $this>
     */
    public function projetoTim(): BelongsTo
    {
        return $this->belongsTo(TimProjeto::class, 'projeto_tim_id');
    }

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @return BelongsTo<RadioLink, $this>
     */
    public function radioLink(): BelongsTo
    {
        return $this->belongsTo(RadioLink::class);
    }

    /**
     * @return BelongsTo<Estacao, $this>
     */
    public function estacaoA(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_a_id');
    }

    /**
     * @return BelongsTo<Estacao, $this>
     */
    public function estacaoB(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_b_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * @return HasMany<OrdemServicoAnexo, $this>
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(OrdemServicoAnexo::class)->orderByDesc('created_at');
    }

    /**
     * @return HasMany<OrdemServicoComentario, $this>
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(OrdemServicoComentario::class)->orderBy('created_at');
    }
}
