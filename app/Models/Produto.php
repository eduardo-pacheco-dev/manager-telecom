<?php

namespace App\Models;

use Database\Factories\ProdutoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string|null $codigo
 * @property string|null $categoria
 * @property string|null $descricao
 * @property string|null $preco
 * @property string|null $observacoes
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'nome', 'codigo', 'categoria', 'descricao', 'preco', 'observacoes', 'ativo',
])]
class Produto extends Model
{
    public const CATEGORIAS = ['Equipamento', 'Infraestrutura', 'Acessório', 'Cabeamento'];

    /** @use HasFactory<ProdutoFactory> */
    use HasFactory;

    protected $table = 'produtos';

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }
}
