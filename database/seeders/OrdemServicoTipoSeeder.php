<?php

namespace Database\Seeders;

use App\Models\OrdemServico;
use App\Models\OrdemServicoTipo;
use Illuminate\Database\Seeder;

class OrdemServicoTipoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (OrdemServico::TIPOS as $tipo) {
            OrdemServicoTipo::updateOrCreate(
                ['nome' => $tipo],
                ['ativo' => true],
            );
        }
    }
}