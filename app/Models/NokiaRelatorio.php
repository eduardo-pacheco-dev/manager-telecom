<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $projeto_nokia_id
 * @property int|null $ordem_servico_id
 * @property int|null $estacao_id
 * @property Carbon|null $data_inicio
 * @property Carbon|null $data_planejada
 * @property Carbon|null $data_real
 * @property string $status
 * @property string|null $observacao
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property NokiaProjeto $projetoNokia
 * @property OrdemServico|null $ordemServico
 * @property Estacao|null $estacao
 */
class NokiaRelatorio extends Model
{
    public const STATUS = ['Pendente', 'Em andamento', 'Concluído', 'Cancelado'];

    protected $table = 'nokia_relatorios';

    protected $fillable = [
        'projeto_nokia_id', 'ordem_servico_id', 'estacao_id',
        'data_inicio', 'data_planejada', 'data_real', 'status', 'observacao', 'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_planejada' => 'date',
            'data_real' => 'date',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<NokiaProjeto, $this>
     */
    public function projetoNokia(): BelongsTo
    {
        return $this->belongsTo(NokiaProjeto::class, 'projeto_nokia_id');
    }

    /**
     * @return BelongsTo<OrdemServico, $this>
     */
    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

    /**
     * @return BelongsTo<Estacao, $this>
     */
    public function estacao(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_id');
    }
}
