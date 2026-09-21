<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $projeto_nokia_id
 * @property string $etapa
 * @property string $status
 * @property Carbon|null $data_conclusao
 * @property Carbon|null $data_baseline
 * @property Carbon|null $data_planejada
 * @property Carbon|null $data_real
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property NokiaProjeto $projeto
 */
class NokiaProjetoEtapa extends Model
{
    public const STATUS = ['Pendente', 'Em andamento', 'Concluída'];

    protected $table = 'nokia_projeto_etapas';

    protected $fillable = ['projeto_nokia_id', 'etapa', 'status', 'data_conclusao', 'data_baseline', 'data_planejada', 'data_real'];

    protected function casts(): array
    {
        return [
            'data_conclusao' => 'date',
            'data_baseline' => 'date',
            'data_planejada' => 'date',
            'data_real' => 'date',
        ];
    }

    /**
     * @return BelongsTo<NokiaProjeto, $this>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(NokiaProjeto::class, 'projeto_nokia_id');
    }
}
