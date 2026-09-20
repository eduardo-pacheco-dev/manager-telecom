<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $arquivo
 * @property string $nome_original
 * @property string $status
 * @property int|null $total_linhas
 * @property int $processadas
 * @property int $importadas
 * @property int $ignoradas
 * @property string|null $erro
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 */
#[Fillable([
    'user_id', 'arquivo', 'nome_original', 'status',
    'total_linhas', 'processadas', 'importadas', 'ignoradas', 'erro',
])]
class ServicoImport extends Model
{
    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_PROCESSANDO = 'processando';

    public const STATUS_CONCLUIDO = 'concluido';

    public const STATUS_FALHOU = 'falhou';

    protected $table = 'servico_imports';

    protected function casts(): array
    {
        return [
            'total_linhas' => 'integer',
            'processadas' => 'integer',
            'importadas' => 'integer',
            'ignoradas' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
