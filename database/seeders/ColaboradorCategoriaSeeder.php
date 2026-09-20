<?php

namespace Database\Seeders;

use App\Models\Colaborador;
use App\Models\ColaboradorCategoria;
use Illuminate\Database\Seeder;

class ColaboradorCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Colaborador::CATEGORIAS as $categoria) {
            ColaboradorCategoria::updateOrCreate(
                ['nome' => $categoria],
                ['ativo' => true],
            );
        }
    }
}
