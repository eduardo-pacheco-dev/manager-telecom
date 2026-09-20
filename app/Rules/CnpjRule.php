<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = preg_replace('/\D/', '', (string) $value);

        if ($cnpj === null || strlen($cnpj) !== 14) {
            $fail(__('O CNPJ deve conter 14 dígitos.'));

            return;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            $fail(__('O CNPJ informado é inválido.'));

            return;
        }

        if (! $this->checkDigits($cnpj)) {
            $fail(__('O CNPJ informado é inválido.'));
        }
    }

    private function checkDigits(string $cnpj): bool
    {
        $length = 12;
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $firstDigit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        $sum = 0;
        $length = 13;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $secondDigit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return (int) $cnpj[12] === $firstDigit && (int) $cnpj[13] === $secondDigit;
    }
}
