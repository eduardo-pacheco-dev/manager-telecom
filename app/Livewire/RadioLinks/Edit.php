<?php

namespace App\Livewire\RadioLinks;

use App\Models\Estacao;
use App\Models\RadioLink;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Radio Link')]
class Edit extends Component
{
    public ?RadioLink $radioLink = null;

    public string $codigo = '';

    public string $nome = '';

    public ?string $estacao_a_id = null;

    public ?string $estacao_b_id = null;

    public ?string $frequencia = null;

    public string $capacidade = '';

    public string $canal = '';

    public string $polarizacao = '';

    public string $fabricante = '';

    public string $modelo = '';

    public ?string $distancia = null;

    public string $status = '';

    public ?string $data_ativacao = null;

    public string $observacao = '';

    public function mount(RadioLink $radioLink): void
    {
        $this->radioLink = $radioLink;
        $this->codigo = $radioLink->codigo;
        $this->nome = $radioLink->nome ?? '';
        $this->estacao_a_id = (string) $radioLink->estacao_a_id;
        $this->estacao_b_id = (string) $radioLink->estacao_b_id;
        $this->frequencia = $radioLink->frequencia !== null ? (string) $radioLink->frequencia : null;
        $this->capacidade = $radioLink->capacidade ?? '';
        $this->canal = $radioLink->canal ?? '';
        $this->polarizacao = $radioLink->polarizacao ?? '';
        $this->fabricante = $radioLink->fabricante ?? '';
        $this->modelo = $radioLink->modelo ?? '';
        $this->distancia = $radioLink->distancia !== null ? (string) $radioLink->distancia : null;
        $this->status = $radioLink->status ?? '';
        $this->data_ativacao = $radioLink->data_ativacao?->format('Y-m-d');
        $this->observacao = $radioLink->observacao ?? '';
    }

    public function save(): void
    {
        $this->normalizeDecimals();

        $validated = $this->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:radio_links,codigo,'.$this->radioLink->id],
            'nome' => ['nullable', 'string', 'max:255'],
            'estacao_a_id' => ['required', 'exists:estacoes,id', 'different:estacao_b_id'],
            'estacao_b_id' => ['required', 'exists:estacoes,id'],
            'frequencia' => ['nullable', 'numeric', 'min:0'],
            'capacidade' => ['nullable', 'string', 'max:255'],
            'canal' => ['nullable', 'string', 'max:255'],
            'polarizacao' => ['nullable', Rule::in(RadioLink::POLARIZACOES)],
            'fabricante' => ['nullable', Rule::in(RadioLink::FABRICANTES)],
            'modelo' => ['nullable', 'string', 'max:255'],
            'distancia' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(RadioLink::STATUS)],
            'data_ativacao' => ['nullable', 'date'],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated = array_map(
            fn (mixed $value): mixed => $value === '' ? null : $value,
            $validated
        );

        $this->radioLink->update($validated);

        Flux::toast(variant: 'success', text: __('Radio link atualizado com sucesso.'));

        $this->redirect(route('radio-links.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.radio-links.edit', [
            'estacoes' => $this->estacoesDisponiveis(),
        ]);
    }

    /**
     * @return Collection<int, Estacao>
     */
    private function estacoesDisponiveis(): Collection
    {
        return Estacao::orderBy('site_id')->get();
    }

    private function normalizeDecimals(): void
    {
        foreach (['frequencia', 'distancia'] as $campo) {
            if (is_string($this->{$campo}) && str_contains($this->{$campo}, ',')) {
                $this->{$campo} = str_replace(',', '.', $this->{$campo});
            }
        }
    }
}
