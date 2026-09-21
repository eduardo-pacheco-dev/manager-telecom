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

    public string $descricao = '';

    public ?string $preco = null;

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
        $validated = $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:servicos,codigo'],
            'categoria' => ['required', Rule::in($this->categorias())],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'preco' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'ativo' => ['boolean'],
        ]);

        Servico::create($validated);

        Flux::toast(variant: 'success', text: __('Serviço criado com sucesso.'));

        $this->redirect(route('servicos.index'), navigate: true);
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
