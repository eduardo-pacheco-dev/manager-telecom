<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $projeto_tim_id
 * @property string $categoria
 * @property string $nome
 * @property string $arquivo
 * @property string|null $mime
 * @property int|null $tamanho
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property TimProjeto $projeto
 */
#[Fillable(['projeto_tim_id', 'categoria', 'nome', 'arquivo', 'mime', 'tamanho'])]
class TimProjetoAnexo extends Model
{
    public const CATEGORIAS = ['TSSR', 'DOC-D', 'Notas Fiscais'];

    protected $table = 'tim_projeto_anexos';

    protected function casts(): array
    {
        return [
            'tamanho' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<TimProjeto, $this>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(TimProjeto::class, 'projeto_tim_id');
    }
}