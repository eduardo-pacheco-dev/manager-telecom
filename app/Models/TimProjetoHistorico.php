<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $projeto_tim_id
 * @property string $tipo
 * @property string $descricao
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property TimProjeto $projeto
 * @property User|null $user
 */
class TimProjetoHistorico extends Model
{
    public const TIPO_CRIACAO = 'criacao';

    public const TIPO_OS_VINCULADA = 'os_vinculada';

    public const TIPO_OS_DESVINCULADA = 'os_desvinculada';

    public const TIPO_ETAPA_ALTERADA = 'etapa_alterada';

    public const TIPO_RELATORIO_CRIADO = 'relatorio_criado';

    public const TIPO_RELATORIO_EXCLUIDO = 'relatorio_excluido';

    public const TIPO_ESTACAO_VINCULADA = 'estacao_vinculada';

    protected $table = 'tim_projeto_historicos';

    protected $fillable = ['projeto_tim_id', 'tipo', 'descricao', 'user_id'];

    /**
     * @return BelongsTo<TimProjeto, $this>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(TimProjeto::class, 'projeto_tim_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}