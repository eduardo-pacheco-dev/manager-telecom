<?php

use App\Livewire\OrdensServico\Create;
use App\Livewire\OrdensServico\Edit;
use App\Livewire\OrdensServico\Index;
use App\Livewire\OrdensServico\Show;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\OrdemServicoAnexo;
use App\Models\RadioLink;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('ordens-servico index page is displayed', function () {
    OrdemServico::factory()->count(3)->create();

    $this->get(route('ordens-servico.index'))->assertOk();
});

test('ordens-servico index shows list', function () {
    OrdemServico::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Título')
        ->assertSee('Prioridade')
        ->assertSee('Status');
});

test('ordens-servico index shows stats', function () {
    OrdemServico::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de ordens')
        ->assertSee('Em aberto')
        ->assertSee('Concluídas')
        ->assertSee('Urgentes');
});

test('ordens-servico can be searched by codigo', function () {
    OrdemServico::factory()->create(['codigo' => 'OS-1001']);
    OrdemServico::factory()->create(['codigo' => 'OS-2002']);

    Livewire::test(Index::class)
        ->set('search', 'OS-1001')
        ->assertSee('OS-1001')
        ->assertDontSee('OS-2002');
});

test('ordens-servico can be filtered by status', function () {
    OrdemServico::factory()->create(['status' => 'Aberta', 'codigo' => 'OS-1001']);
    OrdemServico::factory()->create(['status' => 'Concluída', 'codigo' => 'OS-2002']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'Aberta')
        ->assertSee('OS-1001')
        ->assertDontSee('OS-2002');
});

test('ordens-servico filters can be cleared', function () {
    OrdemServico::factory()->create(['status' => 'Aberta', 'codigo' => 'OS-1001']);

    Livewire::test(Index::class)
        ->set('search', 'OS')
        ->set('filtroStatus', 'Aberta')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroStatus', '');
});

test('ordem de servico can be created', function () {
    $radioLink = RadioLink::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'OS-1001')
        ->set('titulo', 'Manutenção do link')
        ->set('escopo', 'Enlace')
        ->set('radio_link_id', $radioLink->id)
        ->set('status', 'Aberta')
        ->set('prioridade', 'Alta')
        ->set('data_abertura', '2026-09-18')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ordens_servico', [
        'codigo' => 'OS-1001',
        'titulo' => 'Manutenção do link',
        'escopo' => 'Enlace',
        'radio_link_id' => $radioLink->id,
        'status' => 'Aberta',
        'prioridade' => 'Alta',
    ]);
});

test('ordem de servico codigo is auto-generated', function () {
    Livewire::test(Create::class)
        ->set('titulo', 'Serviço')
        ->set('escopo', 'Outro')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ordens_servico', [
        'codigo' => 'OS-0001',
        'titulo' => 'Serviço',
    ]);
});

test('ordem de servico codigo auto-generated is sequential', function () {
    OrdemServico::factory()->create(['codigo' => 'OS-0005']);

    Livewire::test(Create::class)
        ->set('titulo', 'Serviço')
        ->set('escopo', 'Outro')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ordens_servico', [
        'codigo' => 'OS-0006',
        'titulo' => 'Serviço',
    ]);
});

test('ordem de servico creation requires titulo', function () {
    Livewire::test(Create::class)
        ->set('escopo', 'Outro')
        ->call('save')
        ->assertHasErrors(['titulo']);
});

test('ordem de servico creation requires radio link when escopo is enlace', function () {
    Livewire::test(Create::class)
        ->set('codigo', 'OS-2001')
        ->set('titulo', 'Serviço')
        ->set('escopo', 'Enlace')
        ->call('save')
        ->assertHasErrors(['radio_link_id']);
});

test('ordem de servico creation requires estacao when escopo is estacao', function () {
    Livewire::test(Create::class)
        ->set('codigo', 'OS-2002')
        ->set('titulo', 'Serviço')
        ->set('escopo', 'Estação')
        ->call('save')
        ->assertHasErrors(['estacao_a_id']);
});

test('ordem de servico can be created with estacao escopo', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'OS-2003')
        ->set('titulo', 'Serviço na estação')
        ->set('escopo', 'Estação')
        ->set('estacao_a_id', $estacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ordens_servico', [
        'codigo' => 'OS-2003',
        'escopo' => 'Estação',
        'estacao_a_id' => $estacao->id,
    ]);
});

test('ordem de servico creation rejects invalid status', function () {
    Livewire::test(Create::class)
        ->set('codigo', 'OS-1001')
        ->set('titulo', 'Serviço')
        ->set('status', 'INVALIDO')
        ->call('save')
        ->assertHasErrors(['status']);
});

test('selecting a radio link fills stations automatically', function () {
    $radioLink = RadioLink::factory()->create();

    Livewire::test(Create::class)
        ->set('radio_link_id', $radioLink->id)
        ->assertSet('estacao_a_id', (string) $radioLink->estacao_a_id)
        ->assertSet('estacao_b_id', (string) $radioLink->estacao_b_id);
});

test('ordem de servico can be edited', function () {
    $ordem = OrdemServico::factory()->create(['codigo' => 'OS-1001']);

    Livewire::test(Edit::class, ['ordemServico' => $ordem])
        ->set('titulo', 'Título atualizado')
        ->set('status', 'Concluída')
        ->call('save')
        ->assertHasNoErrors();

    $ordem->refresh();

    expect($ordem->titulo)->toEqual('Título atualizado');
    expect($ordem->status)->toEqual('Concluída');
});

test('ordem de servico edit excludes own codigo from uniqueness check', function () {
    $ordem = OrdemServico::factory()->create(['codigo' => 'OS-1001']);

    Livewire::test(Edit::class, ['ordemServico' => $ordem])
        ->set('codigo', 'OS-1001')
        ->call('save')
        ->assertHasNoErrors();
});

test('ordem de servico show page is displayed', function () {
    $ordem = OrdemServico::factory()->create(['status' => 'Aberta']);

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->assertSee($ordem->codigo)
        ->assertSee('Identificação')
        ->assertSee('Enlace')
        ->assertSee('Cronograma')
        ->assertSee('Descrição')
        ->assertSee('Anexos')
        ->assertSee('Comentários')
        ->assertSee('Navegação rápida');
});

test('comentario can be added', function () {
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->set('comentario', '   Equipe acionada.   ')
        ->call('addComentario')
        ->assertHasNoErrors()
        ->assertSet('comentario', '');

    $this->assertDatabaseHas('ordem_servico_comentarios', [
        'ordem_servico_id' => $ordem->id,
        'user_id' => $this->user->id,
        'conteudo' => 'Equipe acionada.',
    ]);
});

test('comentario requires content', function () {
    Livewire::test(Show::class, ['ordemServico' => OrdemServico::factory()->create()])
        ->call('addComentario')
        ->assertHasErrors(['comentario']);
});

test('comentario can be removed by its author', function () {
    $ordem = OrdemServico::factory()->create();
    $comentario = $ordem->comentarios()->create([
        'user_id' => $this->user->id,
        'conteudo' => 'Teste de remoção.',
    ]);

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->call('removerComentario', $comentario->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('ordem_servico_comentarios', ['id' => $comentario->id]);
});

test('comentario can only be removed by its author', function () {
    $ordem = OrdemServico::factory()->create();
    $outroAutor = User::factory()->create();
    $comentario = $ordem->comentarios()->create([
        'user_id' => $outroAutor->id,
        'conteudo' => 'Comentário de outro usuário.',
    ]);

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->call('removerComentario', $comentario->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('ordem_servico_comentarios', ['id' => $comentario->id]);
});

test('anexo can be uploaded', function () {
    Storage::fake('local');
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->set('anexo_arquivo', UploadedFile::fake()->create('laudo.pdf', 2048, 'application/pdf'))
        ->call('saveAnexo')
        ->assertHasNoErrors()
        ->assertSet('anexo_arquivo', null);

    $this->assertDatabaseHas('ordem_servico_anexos', [
        'ordem_servico_id' => $ordem->id,
        'nome' => 'laudo.pdf',
        'mime' => 'application/pdf',
    ]);

    $anexo = OrdemServicoAnexo::where('ordem_servico_id', $ordem->id)->first();

    expect($anexo)->not->toBeNull();
    Storage::disk('local')->assertExists($anexo->arquivo);
});

test('anexo upload requires a file', function () {
    Livewire::test(Show::class, ['ordemServico' => OrdemServico::factory()->create()])
        ->call('saveAnexo')
        ->assertHasErrors(['anexo_arquivo']);
});

test('anexo can be removed', function () {
    Storage::fake('local');
    $ordem = OrdemServico::factory()->create();
    $anexo = $ordem->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/ordem-servico/'.$ordem->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->call('removerAnexo', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('ordem_servico_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('anexo can only be removed from its own ordem', function () {
    $ordem = OrdemServico::factory()->create();
    $outraOrdem = OrdemServico::factory()->create();
    $anexo = $outraOrdem->anexos()->create([
        'nome' => 'laudo.txt',
        'arquivo' => 'anexos/ordem-servico/'.$outraOrdem->id.'/laudo.txt',
        'mime' => 'text/plain',
    ]);

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->call('removerAnexo', $anexo->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('ordem_servico_anexos', ['id' => $anexo->id]);
});

test('anexo can be downloaded', function () {
    Storage::fake('local');
    $ordem = OrdemServico::factory()->create();
    $anexo = $ordem->anexos()->create([
        'nome' => 'projeto.pdf',
        'arquivo' => 'anexos/ordem-servico/'.$ordem->id.'/projeto.pdf',
        'mime' => 'application/pdf',
    ]);
    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    $this->get(route('ordens-servico.anexos.download', $anexo))
        ->assertOk()
        ->assertDownload($anexo->nome);
});

test('ordem de servico can be deleted', function () {
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['ordemServico' => $ordem])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('ordens_servico', ['id' => $ordem->id]);
});

test('unauthenticated user cannot access ordens de servico', function () {
    auth()->logout();

    $this->get(route('ordens-servico.index'))->assertRedirect(route('login'));
    $this->get(route('ordens-servico.create'))->assertRedirect(route('login'));
});

test('ordens-servico page requires authentication', function () {
    $this->get(route('ordens-servico.index'))->assertOk();
});

test('ordens-servico can be sorted by abertura', function () {
    OrdemServico::factory()->create(['codigo' => 'OS-A', 'data_abertura' => '2020-01-01']);
    OrdemServico::factory()->create(['codigo' => 'OS-B', 'data_abertura' => '2023-01-01']);

    Livewire::test(Index::class)
        ->set('perPage', 1)
        ->call('sortBy', 'data_abertura')
        ->assertSet('sortDirection', 'asc')
        ->assertSee('OS-A')
        ->assertDontSee('OS-B');
});

test('ordens-servico sorting ignores unknown fields', function () {
    OrdemServico::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->call('sortBy', 'senha')
        ->assertSet('sortField', 'codigo')
        ->assertSet('sortDirection', 'asc');
});
