<?php

namespace Database\Factories;

use App\Models\OrdemServico;
use App\Models\RadioLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrdemServico>
 */
class OrdemServicoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->bothify('OS-####'),
            'titulo' => fake()->sentence(4),
            'tipo' => fake()->randomElement(OrdemServico::TIPOS),
            'status' => fake()->randomElement(OrdemServico::STATUS),
            'prioridade' => fake()->randomElement(OrdemServico::PRIORIDADES),
            'radio_link_id' => RadioLink::factory(),
            'solicitante' => fake()->name(),
            'responsavel_id' => User::factory(),
            'descricao' => fake()->optional(0.8)->paragraph(),
            'data_abertura' => fake()->dateTimeBetween('-1 year', 'now'),
            'data_agendamento' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'data_conclusao' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function aberta(): static
    {
        return $this->state(fn () => ['status' => 'Aberta']);
    }

    public function concluida(): static
    {
        return $this->state(fn () => ['status' => 'Concluída']);
    }
}
