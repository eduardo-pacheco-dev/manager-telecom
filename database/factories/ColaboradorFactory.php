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
            'cpf' => $this->validCpf(),
            'telefone' => sprintf('(%s) %s-%s', fake()->numerify('##'), fake()->numerify(fake()->boolean(80) ? '#####' : '####'), fake()->numerify('####')),
            'cargo' => fake()->randomElement(['Técnico', 'Engenheiro', 'Analista', 'Supervisor', 'Gerente', 'Coordenador']),
            'departamento' => fake()->randomElement(['Operações', 'Manutenção', 'Comercial', 'Administrativo', 'Financeiro', 'TI']),
            'categoria' => fake()->randomElement(Colaborador::CATEGORIAS),
            'data_admissao' => fake()->dateTimeBetween('-5 years', 'now'),
            'salario' => fake()->randomFloat(2, 2500, 15000),
            'endereco' => fake()->streetAddress(),
            'cidade' => fake()->city(),
            'estado' => fake()->randomElement(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']),
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

    private function validCpf(): string
    {
        $base = str_pad((string) fake()->unique()->randomNumber(9), 9, '0', STR_PAD_LEFT);
        $firstDigit = $this->checkDigit($base);
        $secondDigit = $this->checkDigit($base.$firstDigit, 10);

        return vsprintf('%s.%s.%s-%s%s', [
            substr($base, 0, 3),
            substr($base, 3, 3),
            substr($base, 6, 3),
            $firstDigit,
            $secondDigit,
        ]);
    }

    private function checkDigit(string $digits, int $length = 9): int
    {
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * ($length + 1 - $i);
        }

        $rest = $sum % 11;

        return $rest < 2 ? 0 : 11 - $rest;
    }
}
