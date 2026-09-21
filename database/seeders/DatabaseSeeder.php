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
            ColaboradorCategoriaSeeder::class,
            ColaboradorCargoSeeder::class,
            ColaboradorDepartamentoSeeder::class,
            ColaboradorSeeder::class,
            ClienteSegmentoSeeder::class,
            ClienteSeeder::class,
            ProdutoSeeder::class,
            ServicoCategoriaSeeder::class,
            ServicoSeeder::class,
            EstacaoConfiguracoesSeeder::class,
            EstacaoSeeder::class,
            RadioLinkSeeder::class,
            OrdemServicoTipoSeeder::class,
            OrdemServicoSeeder::class,
        ]);
    }
}
