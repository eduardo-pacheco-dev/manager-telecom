<?php

namespace Database\Seeders;

use App\Models\ColaboradorCargo;
use Illuminate\Database\Seeder;

class ColaboradorCargoSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            'Técnico de Campo',
            'Técnico de Fibra Óptica',
            'Instalador Residencial',
            'Engenheiro de Redes',
            'Analista de NOC',
            'Analista de Suporte',
            'Analista de Cobrança',
            'Analista Financeiro',
            'Assistente Administrativo',
            'Consultor de Vendas',
            'Controlador de Qualidade',
            'Coordenador Comercial',
            'Desenvolvedor de Sistemas',
            'Supervisor de Operações',
            'Gerente de Operações',
        ];

        foreach ($cargos as $cargo) {
            ColaboradorCargo::updateOrCreate(
                ['nome' => $cargo],
                ['ativo' => true],
            );
        }
    }
}
