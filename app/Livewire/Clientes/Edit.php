<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use App\Rules\CnpjRule;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Cliente')]
class Edit extends Component
{
    public ?Cliente $cliente = null;

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

    public function mount(Cliente $cliente): void
    {
        $this->cliente = $cliente;
        $this->nome = $cliente->nome;
        $this->email = $cliente->email;
        $this->documento = $cliente->documento;
        $this->telefone = $cliente->telefone ?? '';
        $this->segmento = $cliente->segmento ?? '';
        $this->endereco = $cliente->endereco ?? '';
        $this->cidade = $cliente->cidade ?? '';
        $this->estado = $cliente->estado ?? '';
        $this->cep = $cliente->cep ?? '';
        $this->observacoes = $cliente->observacoes ?? '';
        $this->ativo = $cliente->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clientes,email,'.$this->cliente->id],
            'documento' => ['required', 'string', new CnpjRule, 'unique:clientes,documento,'.$this->cliente->id],
            'telefone' => ['nullable', 'string', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'segmento' => ['nullable', Rule::in(Cliente::SEGMENTOS)],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $this->cliente->update($validated);

        Flux::toast(variant: 'success', text: __('Cliente atualizado com sucesso.'));

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

    public function render(): View
    {
        return view('livewire.clientes.edit', [
            'segmentos' => Cliente::SEGMENTOS,
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
