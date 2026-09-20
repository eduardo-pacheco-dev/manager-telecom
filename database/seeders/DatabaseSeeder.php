<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        $this->call([
            ColaboradorSeeder::class,
            ClienteSeeder::class,
            ProdutoSeeder::class,
            ServicoSeeder::class,
            EstacaoSeeder::class,
            RadioLinkSeeder::class,
            OrdemServicoTipoSeeder::class,
            OrdemServicoSeeder::class,
        ]);
    }
}
