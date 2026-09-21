<?php

namespace App\Livewire\Servicos;

use App\Models\Servico;
use App\Models\ServicoCategoria;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Serviço')]
class Create extends Component
{
    public string $nome = '';

    public string $codigo = '';

    public string $categoria = '';

    public string $tipo_valor = 'servico';

    public string $descricao = '';

    public ?string $preco = null;

    public ?string $preco_medio = null;

    public ?string $tempo_medio_horas = null;

    public string $observacoes = '';

    public bool $ativo = true;

    public function mount(): void
    {
        $this->codigo = $this->gerarCodigo();
    }

    public function gerarCodigo(): string
    {
        $ultimo = Servico::query()
            ->where('codigo', 'like', 'SRV-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'SRV-'))
            ->max() ?? 0;

        return 'SRV-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $this->normalizeDecimals();

        $regrasPreco = $this->tipo_valor === 'hora'
            ? ['nullable', 'numeric', 'min:0']
            : ['nullable', 'numeric', 'min:0'];

        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:servicos,codigo'],
            'categoria' => ['required', Rule::in($this->categorias())],
            'tipo_valor' => ['required', Rule::in(Servico::TIPOS_VALOR)],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => $regrasPreco,
            'preco_medio' => ['nullable', 'numeric', 'min:0'],
            'tempo_medio_horas' => ['nullable', 'numeric', 'min:0', 'max:168'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        if ($this->tipo_valor === 'servico') {
            $validated['preco_medio'] = null;
            $validated['tempo_medio_horas'] = null;
        } else {
            $validated['preco'] = null;
        }

        Servico::create($validated);

        Flux::toast(variant: 'success', text: __('Serviço criado com sucesso.'));

        $this->redirect(route('servicos.index'), navigate: true);
    }

    private function normalizeDecimals(): void
    {
        foreach (['preco', 'preco_medio', 'tempo_medio_horas'] as $campo) {
            if (is_string($this->{$campo}) && str_contains($this->{$campo}, ',')) {
                $this->{$campo} = str_replace(',', '.', $this->{$campo});
            }
        }
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function categorias(): array
    {
        $categorias = ServicoCategoria::query()
            ->where('ativo', true)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return $categorias !== [] ? $categorias : Servico::CATEGORIAS;
    }

    public function render(): View
    {
        return view('livewire.servicos.create', [
            'categorias' => $this->categorias(),
        ]);
    }
}
