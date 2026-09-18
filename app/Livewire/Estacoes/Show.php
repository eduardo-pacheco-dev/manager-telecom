<?php

namespace App\Livewire\Estacoes;

use App\Models\Estacao;
use App\Models\EstacaoAnexo;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detalhes da Estação')]
class Show extends Component
{
    use WithFileUploads;

    public ?Estacao $estacao = null;

    public bool $showDeleteModal = false;

    public $anexo_arquivo = null;

    public function mount(Estacao $estacao): void
    {
        $this->estacao = $estacao;
    }

    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    public function saveAnexo(): void
    {
        $this->validate([
            'anexo_arquivo' => ['required', 'file', 'max:20480'],
        ], [
            'anexo_arquivo.required' => __('Escolha um arquivo para anexar.'),
            'anexo_arquivo.file' => __('O valor deve ser um arquivo.'),
            'anexo_arquivo.max' => __('O arquivo não pode ter mais de 20 MB.'),
        ]);

        $destino = 'anexos/estacao/'.$this->estacao->id;

        $caminho = $this->anexo_arquivo->storeAs(
            $destino,
            Str::uuid().'.'.$this->anexo_arquivo->getClientOriginalExtension(),
            'local',
        );

        $this->estacao->anexos()->create([
            'nome' => $this->anexo_arquivo->getClientOriginalName(),
            'arquivo' => $caminho,
            'mime' => $this->anexo_arquivo->getMimeType(),
            'tamanho' => $this->anexo_arquivo->getSize(),
        ]);

        $this->reset('anexo_arquivo');

        $this->dispatch('flux-toast', text: __('Anexo enviado com sucesso.'), variant: 'success');
    }

    public function removerAnexo(EstacaoAnexo $anexo): void
    {
        if ($anexo->estacao_id !== $this->estacao->id) {
            abort(404);
        }

        Storage::disk('local')->delete($anexo->arquivo);
        $anexo->delete();

        $this->dispatch('flux-toast', text: __('Anexo removido.'), variant: 'success');
    }

    public function destroy(): void
    {
        $arquivos = $this->estacao->anexos()->pluck('arquivo')->all();

        $this->estacao->delete();

        Storage::disk('local')->delete($arquivos);

        $this->redirect(route('estacoes.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.estacoes.show', [
            'anexos' => $this->estacao->anexos()->get(),
        ]);
    }
}
