<?php

namespace Database\Factories;

use App\Models\Estacao;
use App\Models\RadioLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RadioLink>
 */
class RadioLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->bothify('RL-####'),
            'nome' => fake()->optional(0.5)->words(3, true),
            'estacao_a_id' => Estacao::factory(),
            'estacao_b_id' => Estacao::factory(),
            'frequencia' => fake()->optional()->randomFloat(3, 5, 80),
            'capacidade' => fake()->optional()->randomElement(['10 Mbps', '100 Mbps', '1 Gbps', '2 Gbps', '10 Gbps']),
            'canal' => fake()->optional()->bothify('?##'),
            'polarizacao' => fake()->optional()->randomElement(RadioLink::POLARIZACOES),
            'fabricante' => fake()->optional()->randomElement(RadioLink::FABRICANTES),
            'modelo' => fake()->optional()->bothify('???-####'),
            'distancia' => fake()->optional()->randomFloat(2, 0.5, 50),
            'status' => fake()->randomElement(RadioLink::STATUS),
            'data_ativacao' => fake()->optional()->dateTimeBetween('-5 years', 'now'),
            'observacao' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn () => ['status' => 'Ativo']);
    }

    public function comCoordenadas(): static
    {
        return $this->state(fn () => [
            'estacao_a_id' => Estacao::factory()->create(['latitude' => -10.925094, 'longitude' => -69.554056]),
            'estacao_b_id' => Estacao::factory()->create(['latitude' => -10.075556, 'longitude' => -67.055611]),
        ]);
    }
}
