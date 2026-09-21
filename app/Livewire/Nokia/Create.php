<?php

namespace App\Livewire\Nokia;

use App\Models\NokiaProjeto;
use App\Models\NokiaProjetoHistorico;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Projeto Nokia')]
class Create extends Component
{
    public string $codigo = '';

    public string $nome = '';

    public string $descricao = '';

    public string $status = 'Em andamento';

    public ?string $data_inicio = null;

    public ?string $data_fim = null;

    public bool $ativo = true;

    public function mount(): void
    {
        $this->codigo = $this->gerarCodigo();
    }

    public function gerarCodigo(): string
    {
        $ultimo = NokiaProjeto::query()
            ->where('codigo', 'like', 'NOK-%')
            ->pluck('codigo')
            ->map(fn (string $codigo): int => (int) Str::after($codigo, 'NOK-'))
            ->max() ?? 0;

        return 'NOK-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:nokia_projetos,codigo'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(NokiaProjeto::STATUS)],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date'],
            'ativo' => ['boolean'],
        ]);

        $projeto = NokiaProjeto::create($validated);

        $projeto->ensureEtapas();

        $projeto->registrarHistorico(
            NokiaProjetoHistorico::TIPO_CRIACAO,
            __('Projeto criado'),
        );

        Flux::toast(variant: 'success', text: __('Projeto Nokia criado com sucesso.'));

        $this->redirect(route('nokia.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.nokia.create');
    }
}
