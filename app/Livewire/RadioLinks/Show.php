<?php

namespace App\Livewire\RadioLinks;

use App\Models\RadioLink;
use App\Models\RadioLinkAnexo;
use App\Models\RadioLinkComentario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detalhes do Radio Link')]
class Show extends Component
{
    use WithFileUploads;

    public ?RadioLink $radioLink = null;

    public bool $showDeleteModal = false;

    public $anexo_arquivo = null;

    public string $comentario = '';

    public function mount(RadioLink $radioLink): void
    {
        $this->radioLink = $radioLink;
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

        $destino = 'anexos/radio-link/'.$this->radioLink->id;

        $caminho = $this->anexo_arquivo->storeAs(
            $destino,
            Str::uuid().'.'.$this->anexo_arquivo->getClientOriginalExtension(),
            'local',
        );

        $this->radioLink->anexos()->create([
            'nome' => $this->anexo_arquivo->getClientOriginalName(),
            'arquivo' => $caminho,
            'mime' => $this->anexo_arquivo->getMimeType(),
            'tamanho' => $this->anexo_arquivo->getSize(),
        ]);

        $this->reset('anexo_arquivo');

        $this->dispatch('flux-toast', text: __('Anexo enviado com sucesso.'), variant: 'success');
    }

    public function removerAnexo(RadioLinkAnexo $anexo): void
    {
        if ($anexo->radio_link_id !== $this->radioLink->id) {
            abort(404);
        }

        Storage::disk('local')->delete($anexo->arquivo);
        $anexo->delete();

        $this->dispatch('flux-toast', text: __('Anexo removido.'), variant: 'success');
    }

    public function addComentario(): void
    {
        $this->validate([
            'comentario' => ['required', 'string', 'max:2000'],
        ], [
            'comentario.required' => __('Escreva um comentário antes de enviar.'),
            'comentario.max' => __('O comentário não pode ter mais de 2.000 caracteres.'),
        ]);

        $this->radioLink->comentarios()->create([
            'user_id' => auth()->id(),
            'conteudo' => trim($this->comentario),
        ]);

        $this->reset('comentario');

        $this->dispatch('flux-toast', text: __('Comentário adicionado.'), variant: 'success');
    }

    public function removerComentario(RadioLinkComentario $comentario): void
    {
        if ($comentario->radio_link_id !== $this->radioLink->id || $comentario->user_id !== auth()->id()) {
            abort(404);
        }

        $comentario->delete();

        $this->dispatch('flux-toast', text: __('Comentário removido.'), variant: 'success');
    }

    public function destroy(): void
    {
        $arquivos = $this->radioLink->anexos()->pluck('arquivo')->all();

        $this->radioLink->delete();

        Storage::disk('local')->delete($arquivos);

        $this->redirect(route('radio-links.index'), navigate: true);
    }

    public function render(): View
    {
        $this->radioLink->load(['estacaoA', 'estacaoB']);

        return view('livewire.radio-links.show', [
            'anexos' => $this->radioLink->anexos()->get(),
            'comentarios' => $this->radioLink->comentarios()->with('user')->get(),
        ]);
    }
}
