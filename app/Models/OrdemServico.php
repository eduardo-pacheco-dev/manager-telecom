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
 * @property string $titulo
 * @property string|null $tipo
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RadioLink|null $radioLink
 * @property Estacao|null $estacaoA
 * @property Estacao|null $estacaoB
 * @property User|null $responsavel
 * @property Collection<int, OrdemServicoAnexo> $anexos
 * @property Collection<int, OrdemServicoComentario> $comentarios
 */
#[Fillable([
    'codigo', 'titulo', 'tipo', 'status', 'prioridade', 'radio_link_id',
    'estacao_a_id', 'estacao_b_id', 'solicitante', 'responsavel_id',
    'descricao', 'data_abertura', 'data_agendamento', 'data_conclusao',
])]
class OrdemServico extends Model
{
    public const TIPOS = ['Manutenção', 'Instalação', 'Ativação', 'Desativação', 'Remoção', 'Inspeção', 'Corretiva', 'Preventiva', 'Outro'];

    public const STATUS = ['Aberta', 'Em andamento', 'Aguardando', 'Concluída', 'Cancelada'];

    public const PRIORIDADES = ['Baixa', 'Média', 'Alta', 'Urgente'];

    /** @use HasFactory<OrdemServicoFactory> */
    use HasFactory;

    protected $table = 'ordens_servico';

    protected function casts(): array
    {
        return [
            'data_abertura' => 'date',
            'data_agendamento' => 'date',
            'data_conclusao' => 'date',
        ];
    }

    public function radioLink(): BelongsTo
    {
        return $this->belongsTo(RadioLink::class);
    }

    public function estacaoA(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_a_id');
    }

    public function estacaoB(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_b_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(OrdemServicoAnexo::class)->orderByDesc('created_at');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(OrdemServicoComentario::class)->orderBy('created_at');
    }
}
