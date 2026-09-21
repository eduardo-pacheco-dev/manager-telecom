<?php

namespace Database\Factories;

use App\Models\NokiaProjeto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NokiaProjeto>
 */
class NokiaProjetoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->numerify('NOK-####'),
            'nome' => fake()->randomElement([
                'Implantação RAN TIM',
                'Modernização 5G',
                'Expansão de cobertura',
                'Ativação de novos sites',
                'Troca de equipamentos',
            ]),
            'descricao' => fake()->optional(0.7)->sentence(),
            'status' => fake()->randomElement(NokiaProjeto::STATUS),
            'data_inicio' => fake()->dateTimeBetween('-1 year', 'now'),
            'data_fim' => fake()->optional(0.6)->dateTimeBetween('now', '+1 year'),
            'ativo' => fake()->boolean(85),
        ];
    }
}
