<?php

namespace App\Models;

use Database\Factories\RadioLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $codigo
 * @property string|null $nome
 * @property int $estacao_a_id
 * @property int $estacao_b_id
 * @property string|null $frequencia
 * @property string|null $capacidade
 * @property string|null $canal
 * @property string|null $polarizacao
 * @property string|null $fabricante
 * @property string|null $modelo
 * @property string|null $distancia
 * @property string|null $status
 * @property Carbon|null $data_ativacao
 * @property string|null $observacao
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Estacao $estacaoA
 * @property Estacao $estacaoB
 * @property Collection<int, RadioLinkAnexo> $anexos
 * @property Collection<int, RadioLinkComentario> $comentarios
 */
#[Fillable([
    'codigo', 'nome', 'estacao_a_id', 'estacao_b_id', 'frequencia',
    'capacidade', 'canal', 'polarizacao', 'fabricante', 'modelo',
    'distancia', 'status', 'data_ativacao', 'observacao',
])]
class RadioLink extends Model
{
    public const POLARIZACOES = ['Horizontal', 'Vertical', 'Dupla'];

    public const FABRICANTES = ['ERICSSON', 'CERAGON', 'HUAWEI', 'ZTE', 'NOKIA', 'AVIAT', 'RADWIN'];

    public const STATUS = ['Em implantação', 'Ativo', 'Inativo', 'Desativado', 'Cancelado'];

    /** @use HasFactory<RadioLinkFactory> */
    use HasFactory;

    protected $table = 'radio_links';

    protected function casts(): array
    {
        return [
            'frequencia' => 'decimal:3',
            'distancia' => 'decimal:2',
            'data_ativacao' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Estacao, $this>
     */
    public function estacaoA(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_a_id');
    }

    /**
     * @return BelongsTo<Estacao, $this>
     */
    public function estacaoB(): BelongsTo
    {
        return $this->belongsTo(Estacao::class, 'estacao_b_id');
    }

    /**
     * @return HasMany<RadioLinkAnexo, $this>
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(RadioLinkAnexo::class)->orderByDesc('created_at');
    }

    /**
     * @return HasMany<RadioLinkComentario, $this>
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(RadioLinkComentario::class)->orderBy('created_at');
    }
}
