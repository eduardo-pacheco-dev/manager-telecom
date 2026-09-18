<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $estacao_id
 * @property string $nome
 * @property string $arquivo
 * @property string|null $mime
 * @property int|null $tamanho
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['estacao_id', 'nome', 'arquivo', 'mime', 'tamanho'])]
class EstacaoAnexo extends Model
{
    protected $table = 'estacao_anexos';

    protected function casts(): array
    {
        return [
            'tamanho' => 'integer',
        ];
    }

    public function estacao(): BelongsTo
    {
        return $this->belongsTo(Estacao::class);
    }
}
