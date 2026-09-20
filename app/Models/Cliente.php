<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $documento
 * @property string|null $telefone
 * @property string|null $segmento
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
    'nome', 'email', 'documento', 'telefone', 'segmento',
    'endereco', 'cidade', 'estado', 'cep', 'observacoes', 'ativo',
])]
class Cliente extends Model
{
    public const SEGMENTOS = ['Residencial', 'Corporativo', 'Varejo', 'Atacado', 'Governo', 'Construtora'];

    /** @use HasFactory<ClienteFactory> */
    use HasFactory;

    protected $table = 'clientes';

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }
}
