<?php

namespace Database\Factories;

use App\Models\Servico;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Servico>
 */
class ServicoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->randomElement([
                'Instalação de Fibra Óptica',
                'Manutenção Corretiva',
                'Ativação de Internet 500MB',
                'Suporte Técnico Remoto',
                'Cabeamento Estruturado',
                'Configuração de Roteador',
                'Telefonia VoIP',
                'Locação de Equipamento',
                'Reativação de Conta',
                'Mudança de Endereço',
            ]),
            'codigo' => fake()->unique()->numerify('SRV-####'),
            'categoria' => fake()->randomElement(['Instalação', 'Manutenção', 'Suporte', 'Configuração']),
            'descricao' => fake()->optional(0.8)->sentence(),
            'preco' => fake()->randomFloat(2, 50, 2000),
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
