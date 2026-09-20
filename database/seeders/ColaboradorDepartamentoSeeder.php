<?php

namespace Database\Seeders;

use App\Models\ColaboradorDepartamento;
use Illuminate\Database\Seeder;

class ColaboradorDepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            'Operações',
            'Manutenção',
            'Comercial',
            'Administrativo',
            'Financeiro',
            'TI',
        ];

        foreach ($departamentos as $departamento) {
            ColaboradorDepartamento::updateOrCreate(
                ['nome' => $departamento],
                ['ativo' => true],
            );
        }
    }
}
