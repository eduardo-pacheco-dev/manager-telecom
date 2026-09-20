<?php

namespace App\Livewire\Colaboradores;

use App\Models\Colaborador;
use App\Models\ColaboradorCargo;
use App\Models\ColaboradorCategoria;
use App\Models\ColaboradorDepartamento;
use App\Rules\CpfRule;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Colaborador')]
class Create extends Component
{
    public string $nome = '';

    public string $email = '';

    public string $cpf = '';

    public string $telefone = '';

    public string $cargo = '';

    public string $departamento = '';

    public string $categoria = '';

    public ?string $data_admissao = null;

    public ?string $salario = null;

    public string $endereco = '';

    public string $cidade = '';

    public string $estado = '';

    public string $cep = '';

    public string $observacoes = '';

    public bool $ativo = true;

    public function save(): void
    {
        $regrasCargo = ['nullable', 'string', 'max:255'];
        if ($this->cargos !== []) {
            $regrasCargo[] = Rule::in($this->cargos);
        }

        $regrasDepartamento = ['nullable', 'string', 'max:255'];
        if ($this->departamentos !== []) {
            $regrasDepartamento[] = Rule::in($this->departamentos);
        }

        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:colaboradores,email'],
            'cpf' => ['required', 'string', new CpfRule, 'unique:colaboradores,cpf'],
            'telefone' => ['nullable', 'string', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'cargo' => $regrasCargo,
            'departamento' => $regrasDepartamento,
            'categoria' => ['required', Rule::in($this->categorias)],
            'data_admissao' => ['nullable', 'date'],
            'salario' => ['nullable', 'numeric', 'min:0'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        Colaborador::create($validated);

        Flux::toast(variant: 'success', text: __('Colaborador criado com sucesso.'));

        $this->redirect(route('colaboradores.index'), navigate: true);
    }

    public function updatedCpf(string $value): void
    {
        $this->cpf = $this->maskCpf($value);
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
    public function categorias(): array
    {
        $categorias = ColaboradorCategoria::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return $categorias !== [] ? $categorias : Colaborador::CATEGORIAS;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function cargos(): array
    {
        return ColaboradorCargo::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function departamentos(): array
    {
        return ColaboradorDepartamento::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();
    }

    public function render(): View
    {
        return view('livewire.colaboradores.create', [
            'categorias' => $this->categorias,
            'cargos' => $this->cargos,
            'departamentos' => $this->departamentos,
        ]);
    }

    private function maskCpf(string $value): string
    {
        $digits = substr(preg_replace('/\D/', '', $value), 0, 11);

        return match (true) {
            strlen($digits) <= 3 => $digits,
            strlen($digits) <= 6 => substr($digits, 0, 3).'.'.substr($digits, 3),
            strlen($digits) <= 9 => substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6),
            default => substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6, 3).'-'.substr($digits, 9),
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
