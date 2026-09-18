<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $estacao_id
 * @property int $user_id
 * @property string $conteudo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
#[Fillable(['estacao_id', 'user_id', 'conteudo'])]
class EstacaoComentario extends Model
{
    protected $table = 'estacao_comentarios';

    public function estacao(): BelongsTo
    {
        return $this->belongsTo(Estacao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
