<?php

namespace Database\Seeders;

use App\Models\Servico;
use App\Models\ServicoCategoria;
use Illuminate\Database\Seeder;

class ServicoCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Servico::CATEGORIAS as $categoria) {
            ServicoCategoria::updateOrCreate(
                ['nome' => $categoria],
                ['ativo' => true],
            );
        }
    }
}
