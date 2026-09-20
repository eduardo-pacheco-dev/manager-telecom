<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->company(),
            'email' => fake()->unique()->companyEmail(),
            'documento' => $this->validCnpj(),
            'telefone' => sprintf('(%s) %s-%s', fake()->numerify('##'), fake()->numerify(fake()->boolean(80) ? '#####' : '####'), fake()->numerify('####')),
            'segmento' => fake()->randomElement(Cliente::SEGMENTOS),
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

    private function validCnpj(): string
    {
        $base = str_pad((string) fake()->unique()->randomNumber(8), 8, '0', STR_PAD_LEFT).'0001';
        $digits = $base;
        $digits .= $this->checkDigit($digits);
        $digits .= $this->checkDigit($digits, 13);

        return vsprintf('%s.%s.%s/%s-%s', [
            substr($digits, 0, 2),
            substr($digits, 2, 3),
            substr($digits, 5, 3),
            substr($digits, 8, 4),
            substr($digits, 12, 2),
        ]);
    }

    private function checkDigit(string $digits, int $length = 12): int
    {
        $weights = $length === 12
            ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
            : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }

        $rest = $sum % 11;

        return $rest < 2 ? 0 : 11 - $rest;
    }
}
