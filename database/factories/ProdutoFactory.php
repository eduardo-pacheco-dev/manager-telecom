<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->randomElement([
                'Roteador Wi-Fi 6',
                'Modem ONU GPON',
                'Caixa de Passagem',
                'Cabos de Fibra Óptica 100m',
                'Switch 24 Portas PoE',
                'Antena Exterior 5GHz',
                'Conversor de Mídia',
                'Rack de Parede 6U',
                'Adaptador de Energia 12V',
                'Oxígeno Pigtail SC/APC',
            ]),
            'codigo' => fake()->unique()->numerify('PROD-####'),
            'categoria' => fake()->randomElement(['Equipamento', 'Infraestrutura', 'Acessório', 'Cabeamento']),
            'descricao' => fake()->optional(0.8)->sentence(),
            'preco' => fake()->randomFloat(2, 30, 2500),
            'observacoes' => fake()->optional(0.3)->sentence(),
            'ativo' => fake()->boolean(85),
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn () => ['ativo' => true]);
    }

    public function inativo(): static
    {
        return $this->state(fn () => ['ativo' => false]);
    }
}
