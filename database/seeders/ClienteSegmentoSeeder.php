<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\ClienteSegmento;
use Illuminate\Database\Seeder;

class ClienteSegmentoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Cliente::SEGMENTOS as $segmento) {
            ClienteSegmento::updateOrCreate(
                ['nome' => $segmento],
                ['ativo' => true],
            );
        }
    }
}
