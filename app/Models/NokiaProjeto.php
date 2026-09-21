<?php

namespace App\Models;

use Database\Factories\NokiaProjetoFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string|null $descricao
 * @property string $status
 * @property Carbon|null $data_inicio
 * @property Carbon|null $data_fim
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection<int, OrdemServico> $ordensServico
 */
class NokiaProjeto extends Model
{
    public const STATUS = ['Planejamento', 'Em andamento', 'Pausado', 'Concluído', 'Cancelado'];

    /** @use HasFactory<NokiaProjetoFactory> */
    use HasFactory;

    protected $table = 'nokia_projetos';

    protected $fillable = [
        'codigo', 'nome', 'descricao', 'status', 'data_inicio', 'data_fim', 'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @return HasMany<OrdemServico, $this>
     */
    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'projeto_nokia_id');
    }
}
