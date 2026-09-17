<?php

namespace Database\Seeders;

use App\Models\Colaborador;
use Illuminate\Database\Seeder;

class ColaboradorSeeder extends Seeder
{
    public function run(): void
    {
        Colaborador::factory()->count(20)->create();
    }
}
