<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $radio_link_id
 * @property string $nome
 * @property string $arquivo
 * @property string|null $mime
 * @property int|null $tamanho
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['radio_link_id', 'nome', 'arquivo', 'mime', 'tamanho'])]
class RadioLinkAnexo extends Model
{
    protected $table = 'radio_link_anexos';

    protected function casts(): array
    {
        return [
            'tamanho' => 'integer',
        ];
    }

    public function radioLink(): BelongsTo
    {
        return $this->belongsTo(RadioLink::class);
    }
}
