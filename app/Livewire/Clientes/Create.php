<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Models\ClienteSegmento;
use App\Rules\CnpjRule;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Cliente')]
class Create extends Component
{
    public string $nome = '';

    public string $email = '';

    public string $documento = '';

    public string $telefone = '';

    public string $segmento = '';

    public string $endereco = '';

    public string $cidade = '';

    public string $estado = '';

    public string $cep = '';

    public string $observacoes = '';

    public bool $ativo = true;

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clientes,email'],
            'documento' => ['required', 'string', new CnpjRule, 'unique:clientes,documento'],
            'telefone' => ['nullable', 'string', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'segmento' => ['nullable', 'string', 'max:255', Rule::in($this->segmentos())],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        Cliente::create($validated);

        Flux::toast(variant: 'success', text: __('Cliente criado com sucesso.'));

        $this->redirect(route('clientes.index'), navigate: true);
    }

    public function updatedDocumento(string $value): void
    {
        $this->documento = $this->maskCnpj($value);
    }

    public function updatedTelefone(string $value): void
    {
        $this->telefone = $this->maskTelefone($value);
    }

    public function updatedCep(string $value): void
    {
        $this->cep = $this->maskCep($value);
    }

    public function updatedEstado(string $value): void
    {
        $this->estado = mb_strtoupper($value);
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function segmentos(): array
    {
        $segmentos = ClienteSegmento::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return $segmentos !== [] ? $segmentos : Cliente::SEGMENTOS;
    }

    public function render(): View
    {
        return view('livewire.clientes.create', [
            'segmentos' => $this->segmentos(),
        ]);
    }

    private function maskCnpj(string $value): string
    {
        $digits = substr(preg_replace('/\D/', '', $value), 0, 14);

        return match (true) {
            strlen($digits) <= 2 => $digits,
            strlen($digits) <= 5 => substr($digits, 0, 2).'.'.substr($digits, 2),
            strlen($digits) <= 8 => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5),
            strlen($digits) <= 12 => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8),
            default => substr($digits, 0, 2).'.'.substr($digits, 2, 3).'.'.substr($digits, 5, 3).'/'.substr($digits, 8, 4).'-'.substr($digits, 12),
        };
    }

    private function maskTelefone(string $value): string
    {
        $digits = substr(preg_replace('/\D/', '', $value), 0, 11);

        if (strlen($digits) <= 2) {
            return $digits;
        }

        $ddd = substr($digits, 0, 2);
        $rest = substr($digits, 2);
        $part1Length = strlen($digits) > 10 ? 5 : 4;

        if (strlen($rest) <= $part1Length) {
            return sprintf('(%s) %s', $ddd, $rest);
        }

        return sprintf('(%s) %s-%s', $ddd, substr($rest, 0, $part1Length), substr($rest, $part1Length, 4));
    }

    private function maskCep(string $value): string
    {
        $digits = substr(preg_replace('/\D/', '', $value), 0, 8);

        return strlen($digits) <= 5 ? $digits : substr($digits, 0, 5).'-'.substr($digits, 5);
    }
}
