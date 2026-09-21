<?php

namespace App\Models;

use Database\Factories\ServicoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $codigo
 * @property string|null $categoria
 * @property string $tipo_valor
 * @property string|null $descricao
 * @property string|null $preco
 * @property string|null $preco_medio
 * @property string|null $tempo_medio_horas
 * @property string|null $observacoes
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'nome', 'codigo', 'categoria', 'tipo_valor', 'descricao', 'preco',
    'preco_medio', 'tempo_medio_horas', 'observacoes', 'ativo',
])]
class Servico extends Model
{
    public const CATEGORIAS = ['Instalação', 'Manutenção', 'Suporte', 'Configuração'];

    public const TIPOS_VALOR = ['servico', 'hora'];

    /** @use HasFactory<ServicoFactory> */
    use HasFactory;

    protected $table = 'servicos';

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'preco_medio' => 'decimal:2',
            'tempo_medio_horas' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }
}
