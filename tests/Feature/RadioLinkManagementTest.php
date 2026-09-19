<?php

use App\Livewire\RadioLinks\Create;
use App\Livewire\RadioLinks\Edit;
use App\Livewire\RadioLinks\Index;
use App\Livewire\RadioLinks\Show;
use App\Models\Estacao;
use App\Models\RadioLink;
use App\Models\RadioLinkAnexo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('radio-links index page is displayed', function () {
    RadioLink::factory()->count(3)->create();

    $this->get(route('radio-links.index'))->assertOk();
});

test('radio-links index shows list', function () {
    RadioLink::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Estações')
        ->assertSee('Frequência')
        ->assertSee('Status');
});

test('radio-links index shows stats', function () {
    RadioLink::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de radio links')
        ->assertSee('Links ativos')
        ->assertSee('Fabricantes')
        ->assertSee('Estações conectadas');
});

test('radio-links can be searched by codigo', function () {
    RadioLink::factory()->create(['codigo' => 'RL-1001']);
    RadioLink::factory()->create(['codigo' => 'RL-2002']);

    Livewire::test(Index::class)
        ->set('search', 'RL-1001')
        ->assertSee('RL-1001')
        ->assertDontSee('RL-2002');
});

test('radio-links can be searched by estacao site id', function () {
    $estacaoA = Estacao::factory()->create(['site_id' => 'AC1001']);
    $estacaoB = Estacao::factory()->create(['site_id' => 'AC1002']);
    RadioLink::factory()->create(['codigo' => 'RL-1001', 'estacao_a_id' => $estacaoA->id, 'estacao_b_id' => $estacaoB->id]);
    RadioLink::factory()->create(['codigo' => 'RL-2002']);

    Livewire::test(Index::class)
        ->set('search', 'AC1001')
        ->assertSee('RL-1001')
        ->assertDontSee('RL-2002');
});

test('radio-links can be filtered by status', function () {
    RadioLink::factory()->create(['status' => 'Ativo', 'codigo' => 'RL-1001']);
    RadioLink::factory()->create(['status' => 'Cancelado', 'codigo' => 'RL-2002']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'Ativo')
        ->assertSee('RL-1001')
        ->assertDontSee('RL-2002');
});

test('radio-links filters can be cleared', function () {
    RadioLink::factory()->create(['status' => 'Ativo', 'codigo' => 'RL-1001']);

    Livewire::test(Index::class)
        ->set('search', 'RL')
        ->set('filtroStatus', 'Ativo')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroStatus', '');
});

test('radio link can be created', function () {
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'RL-1001')
        ->set('estacao_a_id', $estacaoA->id)
        ->set('estacao_b_id', $estacaoB->id)
        ->set('status', 'Ativo')
        ->set('frequencia', '23,000')
        ->set('distancia', '12,5')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('radio_links', [
        'codigo' => 'RL-1001',
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
        'status' => 'Ativo',
        'frequencia' => 23.000,
        'distancia' => 12.5,
    ]);
});

test('radio link creation requires codigo', function () {
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('estacao_a_id', $estacaoA->id)
        ->set('estacao_b_id', $estacaoB->id)
        ->call('save')
        ->assertHasErrors(['codigo']);
});

test('radio link creation requires unique codigo', function () {
    RadioLink::factory()->create(['codigo' => 'RL-1001']);
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'RL-1001')
        ->set('estacao_a_id', $estacaoA->id)
        ->set('estacao_b_id', $estacaoB->id)
        ->call('save')
        ->assertHasErrors(['codigo']);
});

test('radio link creation requires distinct stations', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'RL-1001')
        ->set('estacao_a_id', $estacao->id)
        ->set('estacao_b_id', $estacao->id)
        ->call('save')
        ->assertHasErrors(['estacao_a_id']);
});

test('radio link creation rejects invalid status', function () {
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();

    Livewire::test(Create::class)
        ->set('codigo', 'RL-1001')
        ->set('estacao_a_id', $estacaoA->id)
        ->set('estacao_b_id', $estacaoB->id)
        ->set('status', 'INVALIDO')
        ->call('save')
        ->assertHasErrors(['status']);
});

test('radio link can be edited', function () {
    $radioLink = RadioLink::factory()->create(['codigo' => 'RL-1001']);
    $novaEstacao = Estacao::factory()->create();

    Livewire::test(Edit::class, ['radioLink' => $radioLink])
        ->set('codigo', 'RL-1002')
        ->set('estacao_a_id', $novaEstacao->id)
        ->call('save')
        ->assertHasNoErrors();

    $radioLink->refresh();

    expect($radioLink->codigo)->toEqual('RL-1002');
    expect($radioLink->estacao_a_id)->toEqual($novaEstacao->id);
});

test('radio link edit excludes own codigo from uniqueness check', function () {
    $radioLink = RadioLink::factory()->create(['codigo' => 'RL-1001']);

    Livewire::test(Edit::class, ['radioLink' => $radioLink])
        ->set('codigo', 'RL-1001')
        ->call('save')
        ->assertHasNoErrors();
});

test('radio link show page is displayed', function () {
    $radioLink = RadioLink::factory()->create(['status' => 'Ativo']);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->assertSee($radioLink->codigo)
        ->assertSee('Estações')
        ->assertSee('Mapa')
        ->assertSee('Configuração')
        ->assertSee('Anexos')
        ->assertSee('Comentários')
        ->assertSee('Navegação rápida');
});

test('radio link show page displays map when stations have coordinates', function () {
    $radioLink = RadioLink::factory()->comCoordenadas()->create(['status' => 'Ativo']);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->assertSee('Abrir trajeto no mapa')
        ->assertSee('google.com/maps')
        ->assertDontSee('Mapa indisponível');
});

test('radio link show page hides map when stations lack coordinates', function () {
    $estacaoA = Estacao::factory()->create(['latitude' => null, 'longitude' => null]);
    $estacaoB = Estacao::factory()->create(['latitude' => null, 'longitude' => null]);
    $radioLink = RadioLink::factory()->create([
        'status' => 'Ativo',
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
    ]);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->assertSee('Mapa indisponível')
        ->assertDontSee('Abrir trajeto no mapa');
});

test('comentario can be added', function () {
    $radioLink = RadioLink::factory()->create();

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->set('comentario', '   Link ativado.   ')
        ->call('addComentario')
        ->assertHasNoErrors()
        ->assertSet('comentario', '');

    $this->assertDatabaseHas('radio_link_comentarios', [
        'radio_link_id' => $radioLink->id,
        'user_id' => $this->user->id,
        'conteudo' => 'Link ativado.',
    ]);
});

test('comentario requires content', function () {
    Livewire::test(Show::class, ['radioLink' => RadioLink::factory()->create()])
        ->call('addComentario')
        ->assertHasErrors(['comentario']);
});

test('comentario can be removed by its author', function () {
    $radioLink = RadioLink::factory()->create();
    $comentario = $radioLink->comentarios()->create([
        'user_id' => $this->user->id,
        'conteudo' => 'Teste de remoção.',
    ]);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->call('removerComentario', $comentario->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('radio_link_comentarios', ['id' => $comentario->id]);
});

test('comentario can only be removed by its author', function () {
    $radioLink = RadioLink::factory()->create();
    $outroAutor = User::factory()->create();
    $comentario = $radioLink->comentarios()->create([
        'user_id' => $outroAutor->id,
        'conteudo' => 'Comentário de outro usuário.',
    ]);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->call('removerComentario', $comentario->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('radio_link_comentarios', ['id' => $comentario->id]);
});

test('anexo can be uploaded', function () {
    Storage::fake('local');
    $radioLink = RadioLink::factory()->create();

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->set('anexo_arquivo', UploadedFile::fake()->create('laudo.pdf', 2048, 'application/pdf'))
        ->call('saveAnexo')
        ->assertHasNoErrors()
        ->assertSet('anexo_arquivo', null);

    $this->assertDatabaseHas('radio_link_anexos', [
        'radio_link_id' => $radioLink->id,
        'nome' => 'laudo.pdf',
        'mime' => 'application/pdf',
    ]);

    $anexo = RadioLinkAnexo::where('radio_link_id', $radioLink->id)->first();

    expect($anexo)->not->toBeNull();
    Storage::disk('local')->assertExists($anexo->arquivo);
});

test('anexo upload requires a file', function () {
    Livewire::test(Show::class, ['radioLink' => RadioLink::factory()->create()])
        ->call('saveAnexo')
        ->assertHasErrors(['anexo_arquivo']);
});

test('anexo can be removed', function () {
    Storage::fake('local');
    $radioLink = RadioLink::factory()->create();
    $anexo = $radioLink->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/radio-link/'.$radioLink->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->call('removerAnexo', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('radio_link_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('anexo can only be removed from its own radio link', function () {
    $radioLink = RadioLink::factory()->create();
    $outroLink = RadioLink::factory()->create();
    $anexo = $outroLink->anexos()->create([
        'nome' => 'laudo.txt',
        'arquivo' => 'anexos/radio-link/'.$outroLink->id.'/laudo.txt',
        'mime' => 'text/plain',
    ]);

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->call('removerAnexo', $anexo->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('radio_link_anexos', ['id' => $anexo->id]);
});

test('anexo can be downloaded', function () {
    Storage::fake('local');
    $radioLink = RadioLink::factory()->create();
    $anexo = $radioLink->anexos()->create([
        'nome' => 'projeto.pdf',
        'arquivo' => 'anexos/radio-link/'.$radioLink->id.'/projeto.pdf',
        'mime' => 'application/pdf',
    ]);
    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    $this->get(route('radio-links.anexos.download', $anexo))
        ->assertOk()
        ->assertDownload($anexo->nome);
});

test('radio link can be deleted', function () {
    $radioLink = RadioLink::factory()->create();

    Livewire::test(Show::class, ['radioLink' => $radioLink])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('radio_links', ['id' => $radioLink->id]);
});

test('unauthenticated user cannot access radio links', function () {
    auth()->logout();

    $this->get(route('radio-links.index'))->assertRedirect(route('login'));
    $this->get(route('radio-links.create'))->assertRedirect(route('login'));
});

test('radio-links page requires authentication', function () {
    $this->get(route('radio-links.index'))->assertOk();
});

test('radio-links can be sorted by activation date', function () {
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();
    RadioLink::factory()->create(['codigo' => 'RL-A', 'data_ativacao' => '2020-01-01', 'estacao_a_id' => $estacaoA->id, 'estacao_b_id' => $estacaoB->id]);
    RadioLink::factory()->create(['codigo' => 'RL-B', 'data_ativacao' => '2023-01-01', 'estacao_a_id' => $estacaoA->id, 'estacao_b_id' => $estacaoB->id]);

    Livewire::test(Index::class)
        ->set('perPage', 1)
        ->call('sortBy', 'data_ativacao')
        ->assertSet('sortDirection', 'asc')
        ->assertSee('RL-A')
        ->assertDontSee('RL-B');
});

test('radio-links sorting ignores unknown fields', function () {
    RadioLink::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->call('sortBy', 'senha')
        ->assertSet('sortField', 'codigo')
        ->assertSet('sortDirection', 'asc');
});
