<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $projeto_nokia_id
 * @property string $tipo
 * @property string $descricao
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property NokiaProjeto $projeto
 * @property User|null $user
 */
class NokiaProjetoHistorico extends Model
{
    public const TIPO_CRIACAO = 'criacao';

    public const TIPO_OS_VINCULADA = 'os_vinculada';

    public const TIPO_OS_DESVINCULADA = 'os_desvinculada';

    public const TIPO_ETAPA_ALTERADA = 'etapa_alterada';

    public const TIPO_RELATORIO_CRIADO = 'relatorio_criado';

    public const TIPO_RELATORIO_EXCLUIDO = 'relatorio_excluido';

    public const TIPO_ESTACAO_VINCULADA = 'estacao_vinculada';

    protected $table = 'nokia_projeto_historicos';

    protected $fillable = ['projeto_nokia_id', 'tipo', 'descricao', 'user_id'];

    /**
     * @return BelongsTo<NokiaProjeto, $this>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(NokiaProjeto::class, 'projeto_nokia_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
