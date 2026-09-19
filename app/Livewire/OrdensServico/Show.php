<?php

namespace App\Livewire\OrdensServico;

use App\Models\OrdemServico;
use App\Models\OrdemServicoAnexo;
use App\Models\OrdemServicoComentario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detalhes da Ordem de Serviço')]
class Show extends Component
{
    use WithFileUploads;

    public ?OrdemServico $ordemServico = null;

    public bool $showDeleteModal = false;

    public $anexo_arquivo = null;

    public string $comentario = '';

    public function mount(OrdemServico $ordemServico): void
    {
        $this->ordemServico = $ordemServico;
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

        $destino = 'anexos/ordem-servico/'.$this->ordemServico->id;

        $caminho = $this->anexo_arquivo->storeAs(
            $destino,
            Str::uuid().'.'.$this->anexo_arquivo->getClientOriginalExtension(),
            'local',
        );

        $this->ordemServico->anexos()->create([
            'nome' => $this->anexo_arquivo->getClientOriginalName(),
            'arquivo' => $caminho,
            'mime' => $this->anexo_arquivo->getMimeType(),
            'tamanho' => $this->anexo_arquivo->getSize(),
        ]);

        $this->reset('anexo_arquivo');

        $this->dispatch('flux-toast', text: __('Anexo enviado com sucesso.'), variant: 'success');
    }

    public function removerAnexo(OrdemServicoAnexo $anexo): void
    {
        if ($anexo->ordem_servico_id !== $this->ordemServico->id) {
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

        $this->ordemServico->comentarios()->create([
            'user_id' => auth()->id(),
            'conteudo' => trim($this->comentario),
        ]);

        $this->reset('comentario');

        $this->dispatch('flux-toast', text: __('Comentário adicionado.'), variant: 'success');
    }

    public function removerComentario(OrdemServicoComentario $comentario): void
    {
        if ($comentario->ordem_servico_id !== $this->ordemServico->id || $comentario->user_id !== auth()->id()) {
            abort(404);
        }

        $comentario->delete();

        $this->dispatch('flux-toast', text: __('Comentário removido.'), variant: 'success');
    }

    public function destroy(): void
    {
        $arquivos = $this->ordemServico->anexos()->pluck('arquivo')->all();

        $this->ordemServico->delete();

        Storage::disk('local')->delete($arquivos);

        $this->redirect(route('ordens-servico.index'), navigate: true);
    }

    public function render(): View
    {
        $this->ordemServico->load(['radioLink', 'estacaoA', 'estacaoB', 'responsavel']);

        return view('livewire.ordens-servico.show', [
            'anexos' => $this->ordemServico->anexos()->get(),
            'comentarios' => $this->ordemServico->comentarios()->with('user')->get(),
        ]);
    }
}
