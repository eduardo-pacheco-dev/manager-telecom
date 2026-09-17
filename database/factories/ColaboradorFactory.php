<?php

namespace Database\Factories;

use App\Models\Colaborador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Colaborador>
 */
class ColaboradorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'telefone' => fake()->phoneNumber(),
            'cargo' => fake()->randomElement(['Técnico', 'Engenheiro', 'Analista', 'Supervisor', 'Gerente', 'Coordenador']),
            'departamento' => fake()->randomElement(['Operações', 'Manutenção', 'Comercial', 'Administrativo', 'Financeiro', 'TI']),
            'categoria' => fake()->randomElement(Colaborador::CATEGORIAS),
            'data_admissao' => fake()->dateTimeBetween('-5 years', 'now'),
            'salario' => fake()->randomFloat(2, 2500, 15000),
            'endereco' => fake()->streetAddress(),
            'cidade' => fake()->city(),
            'estado' => fake()->stateAbbr(),
            'cep' => fake()->numerify('#####-###'),
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
