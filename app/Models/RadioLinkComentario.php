<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $radio_link_id
 * @property int $user_id
 * @property string $conteudo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
#[Fillable(['radio_link_id', 'user_id', 'conteudo'])]
class RadioLinkComentario extends Model
{
    protected $table = 'radio_link_comentarios';

    /**
     * @return BelongsTo<RadioLink, $this>
     */
    public function radioLink(): BelongsTo
    {
        return $this->belongsTo(RadioLink::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
