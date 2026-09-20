<?php

namespace App\Models;

use Database\Factories\ColaboradorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $cpf
 * @property string|null $telefone
 * @property string|null $cargo
 * @property string|null $departamento
 * @property string|null $categoria
 * @property Carbon|null $data_admissao
 * @property string|null $salario
 * @property string|null $endereco
 * @property string|null $cidade
 * @property string|null $estado
 * @property string|null $cep
 * @property string|null $observacoes
 * @property bool $ativo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'nome', 'email', 'cpf', 'telefone', 'cargo', 'departamento',
    'categoria', 'data_admissao', 'salario', 'endereco', 'cidade', 'estado',
    'cep', 'observacoes', 'ativo',
])]
class Colaborador extends Model
{
    public const CATEGORIAS = ['CLT', 'PJ', 'Freelancer'];

    /** @use HasFactory<ColaboradorFactory> */
    use HasFactory;

    protected $table = 'colaboradores';

    protected function casts(): array
    {
        return [
            'data_admissao' => 'date',
            'salario' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }
}
