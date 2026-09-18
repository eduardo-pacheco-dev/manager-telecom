<?php

use App\Livewire\Estacoes\Create;
use App\Livewire\Estacoes\Edit;
use App\Livewire\Estacoes\Index;
use App\Livewire\Estacoes\Show;
use App\Models\Estacao;
use App\Models\EstacaoAnexo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('estacoes index page is displayed', function () {
    Estacao::factory()->count(3)->create();

    $this->get(route('estacoes.index'))->assertOk();
});

test('estacoes index shows list', function () {
    Estacao::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Elemento / Tecnologia')
        ->assertSee('Ações');
});

test('estacoes index shows stats', function () {
    Estacao::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de estações')
        ->assertSee('Tecnologias')
        ->assertSee('Municípios')
        ->assertSee('Tipos de elemento');
});

test('estacoes filters can be cleared', function () {
    Estacao::factory()->create(['site_id' => 'AC1001', 'tipo_elemento' => 'NODE B', 'status' => 'Aquisitado']);

    Livewire::test(Index::class)
        ->set('search', 'AC')
        ->set('filtroTipoElemento', 'NODE B')
        ->set('filtroStatus', 'Aquisitado')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroTipoElemento', '')
        ->assertSet('filtroStatus', '');
});

test('estacoes can be searched by site id', function () {
    Estacao::factory()->create(['site_id' => 'AC1001']);
    Estacao::factory()->create(['site_id' => 'DZ0303']);

    Livewire::test(Index::class)
        ->set('search', 'AC1001')
        ->assertSee('AC1001')
        ->assertDontSee('DZ0303');
});

test('estacoes can be searched by municipio', function () {
    Estacao::factory()->create(['municipio' => 'ASSIS BRASIL']);
    Estacao::factory()->create(['municipio' => 'ACRELANDIA']);

    Livewire::test(Index::class)
        ->set('search', 'ASSIS')
        ->assertSee('ASSIS BRASIL')
        ->assertDontSee('ACRELANDIA');
});

test('estacoes can be filtered by tipo de elemento', function () {
    Estacao::factory()->create(['tipo_elemento' => 'NODE B', 'site_id' => 'AC1001']);
    Estacao::factory()->create(['tipo_elemento' => 'BTS', 'site_id' => 'ACLD01']);

    Livewire::test(Index::class)
        ->set('filtroTipoElemento', 'NODE B')
        ->assertSee('AC1001')
        ->assertDontSee('ACLD01');
});

test('estacoes can be filtered by status', function () {
    Estacao::factory()->create(['status' => 'Aquisitado', 'site_id' => 'AC1001']);
    Estacao::factory()->create(['status' => 'Cancelado', 'site_id' => 'DZ0303']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'Aquisitado')
        ->assertSee('AC1001')
        ->assertDontSee('DZ0303');
});

test('estacao can be created', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('tipo_elemento', 'NODE B')
        ->set('tecnologia', 'UMTS')
        ->set('municipio', 'ACRELANDIA')
        ->set('estado', 'AC')
        ->set('status', 'Aquisitado')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('estacoes', [
        'site_id' => 'AC1001',
        'tipo_elemento' => 'NODE B',
        'municipio' => 'ACRELANDIA',
        'status' => 'Aquisitado',
    ]);
});

test('estacao creation requires site_id', function () {
    Livewire::test(Create::class)
        ->set('site_id', '')
        ->call('save')
        ->assertHasErrors(['site_id']);
});

test('estacao creation requires unique site_id', function () {
    Estacao::factory()->create(['site_id' => 'AC1001']);

    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->call('save')
        ->assertHasErrors(['site_id']);
});

test('estacao creation requires a valid tipo de elemento', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('tipo_elemento', 'RADIO')
        ->call('save')
        ->assertHasErrors(['tipo_elemento']);
});

test('estacao creation requires valid estado size', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('estado', 'ACX')
        ->call('save')
        ->assertHasErrors(['estado']);
});

test('estacao creation rejects invalid cep', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('cep', '123')
        ->call('save')
        ->assertHasErrors(['cep']);
});

test('estacao creation rejects invalid latitude', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('latitude', '150')
        ->call('save')
        ->assertHasErrors(['latitude']);
});

test('estacao creation normalizes comma decimals', function () {
    Livewire::test(Create::class)
        ->set('site_id', 'AC1001')
        ->set('latitude', '-10,925094')
        ->set('longitude', '-69,554056')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('estacoes', [
        'site_id' => 'AC1001',
        'latitude' => -10.925094,
        'longitude' => -69.554056,
    ]);
});

test('estacao estado is uppercased', function () {
    Livewire::test(Create::class)
        ->set('estado', 'ac')
        ->assertSet('estado', 'AC');
});

test('estacao can be edited', function () {
    $estacao = Estacao::factory()->create(['site_id' => 'AC1001']);

    Livewire::test(Edit::class, ['estacao' => $estacao])
        ->set('site_id', 'AC1002')
        ->set('municipio', 'ASSIS BRASIL')
        ->call('save')
        ->assertHasNoErrors();

    $estacao->refresh();

    expect($estacao->site_id)->toEqual('AC1002');
    expect($estacao->municipio)->toEqual('ASSIS BRASIL');
});

test('estacao edit excludes own site_id from uniqueness check', function () {
    $estacao = Estacao::factory()->create(['site_id' => 'AC1001']);

    Livewire::test(Edit::class, ['estacao' => $estacao])
        ->set('site_id', 'AC1001')
        ->call('save')
        ->assertHasNoErrors();
});

test('estacao show page is displayed', function () {
    $estacao = Estacao::factory()->create([
        'status' => 'Ativo',
        'classificacao' => 'ACESSO',
        'data_aquisicao' => now(),
    ]);

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->assertSee($estacao->site_id)
        ->assertSee('Ciclo de vida')
        ->assertSee('Identificação')
        ->assertSee('Endereço')
        ->assertSee('Estrutura')
        ->assertSee('Contratos')
        ->assertSee('Anotações')
        ->assertSee('Anexos')
        ->assertSee('Comentários')
        ->assertSee('Navegação rápida');
});

test('comentario can be added', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->set('comentario', '   Torreta instalada.   ')
        ->call('addComentario')
        ->assertHasNoErrors()
        ->assertSet('comentario', '');

    $this->assertDatabaseHas('estacao_comentarios', [
        'estacao_id' => $estacao->id,
        'user_id' => $this->user->id,
        'conteudo' => 'Torreta instalada.',
    ]);
});

test('comentario requires content', function () {
    Livewire::test(Show::class, ['estacao' => Estacao::factory()->create()])
        ->call('addComentario')
        ->assertHasErrors(['comentario']);
});

test('comentario can be removed by its author', function () {
    $estacao = Estacao::factory()->create();
    $comentario = $estacao->comentarios()->create([
        'user_id' => $this->user->id,
        'conteudo' => 'Teste de remoção.',
    ]);

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->call('removerComentario', $comentario->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('estacao_comentarios', ['id' => $comentario->id]);
});

test('comentario can only be removed by its author', function () {
    $estacao = Estacao::factory()->create();
    $outroAutor = User::factory()->create();
    $comentario = $estacao->comentarios()->create([
        'user_id' => $outroAutor->id,
        'conteudo' => 'Comentário de outro usuário.',
    ]);

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->call('removerComentario', $comentario->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('estacao_comentarios', ['id' => $comentario->id]);
});

test('anexo can be uploaded', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->set('anexo_arquivo', UploadedFile::fake()->create('contrato.pdf', 2048, 'application/pdf'))
        ->call('saveAnexo')
        ->assertHasNoErrors()
        ->assertSet('anexo_arquivo', null);

    $this->assertDatabaseHas('estacao_anexos', [
        'estacao_id' => $estacao->id,
        'nome' => 'contrato.pdf',
        'mime' => 'application/pdf',
    ]);

    $anexo = EstacaoAnexo::where('estacao_id', $estacao->id)->first();

    expect($anexo)->not->toBeNull();
    Storage::disk('local')->assertExists($anexo->arquivo);
});

test('anexo upload requires a file', function () {
    Livewire::test(Show::class, ['estacao' => Estacao::factory()->create()])
        ->call('saveAnexo')
        ->assertHasErrors(['anexo_arquivo']);
});

test('anexo can be removed', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();
    $anexo = $estacao->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/estacao/'.$estacao->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->call('removerAnexo', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('estacao_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('anexo can only be removed from its own estacao', function () {
    $estacao = Estacao::factory()->create();
    $outraEstacao = Estacao::factory()->create();
    $anexo = $outraEstacao->anexos()->create([
        'nome' => 'laudo.txt',
        'arquivo' => 'anexos/estacao/'.$outraEstacao->id.'/laudo.txt',
        'mime' => 'text/plain',
    ]);

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->call('removerAnexo', $anexo->id)
        ->assertStatus(404);

    $this->assertDatabaseHas('estacao_anexos', ['id' => $anexo->id]);
});

test('anexo can be downloaded', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();
    $anexo = $estacao->anexos()->create([
        'nome' => 'projeto.pdf',
        'arquivo' => 'anexos/estacao/'.$estacao->id.'/projeto.pdf',
        'mime' => 'application/pdf',
    ]);
    Storage::disk('local')->put($anexo->arquivo, 'conteudo do arquivo');

    $this->get(route('estacoes.anexos.download', $anexo))
        ->assertOk()
        ->assertDownload($anexo->nome);
});

test('estacao can be deleted', function () {
    $estacao = Estacao::factory()->create();

    Livewire::test(Show::class, ['estacao' => $estacao])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('estacoes', ['id' => $estacao->id]);
});

test('unauthenticated user cannot access estacoes', function () {
    auth()->logout();

    $this->get(route('estacoes.index'))->assertRedirect(route('login'));
    $this->get(route('estacoes.create'))->assertRedirect(route('login'));
});

test('estacoes page requires authentication', function () {
    $this->get(route('estacoes.index'))->assertOk();
});

test('estacao with all fields can be created', function () {
    Livewire::test(Create::class)
        ->set('site_id', '4G-ABLAJ1')
        ->set('tipo_elemento', 'ENODE B')
        ->set('tecnologia', 'LTE')
        ->set('tipo_conexao', 'Indefinido')
        ->set('endereco_id', 'ACABL_0001')
        ->set('classificacao', 'RANSHARING')
        ->set('data_aquisicao', '2021-09-29')
        ->set('detentor_area', 'IHS BRAZIL')
        ->set('tipo_contrato_infra', 'Built-to-Suit')
        ->set('detentor_infra', 'IHS BRAZIL')
        ->set('tipo_infra', 'Greenfield')
        ->set('observacao', 'CANDIDATO A')
        ->set('tipo_logradouro', 'RUA')
        ->set('logradouro', 'MANOEL BATISTA DE ARAÚJO')
        ->set('numero', 'S/N')
        ->set('complemento', 'QUADRA 12, LOTE 09')
        ->set('bairro', 'CENTRO')
        ->set('municipio', 'ASSIS BRASIL')
        ->set('estado', 'AC')
        ->set('cep', '69935000')
        ->set('regional', 'TCO')
        ->set('latitude', '-10.925094')
        ->set('longitude', '-69.554056')
        ->set('status', 'Aquisitado')
        ->set('aev_nominal', '0')
        ->set('area_solo', '0')
        ->set('altura_estrutura', '40')
        ->set('station_id', '68010010')
        ->set('ots', 'Não')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('estacoes', [
        'site_id' => '4G-ABLAJ1',
        'tipo_elemento' => 'ENODE B',
        'detentor_area' => 'IHS BRAZIL',
        'municipio' => 'ASSIS BRASIL',
        'latitude' => -10.925094,
        'longitude' => -69.554056,
    ]);
});

test('estacoes can be sorted by acquisition date', function () {
    Estacao::factory()->create(['site_id' => 'AAA1', 'data_aquisicao' => '2020-01-01']);
    Estacao::factory()->create(['site_id' => 'BBB1', 'data_aquisicao' => '2023-01-01']);
    Estacao::factory()->create(['site_id' => 'CCC1', 'data_aquisicao' => '2021-01-01']);

    Livewire::test(Index::class)
        ->set('perPage', 1)
        ->call('sortBy', 'data_aquisicao')
        ->assertSet('sortDirection', 'asc')
        ->assertSee('AAA1')
        ->assertDontSee('BBB1');

    Livewire::test(Index::class)
        ->set('perPage', 1)
        ->call('sortBy', 'data_aquisicao')
        ->call('sortBy', 'data_aquisicao')
        ->assertSet('sortDirection', 'desc')
        ->assertSee('BBB1')
        ->assertDontSee('AAA1');
});

test('estacoes sorting ignores unknown fields', function () {
    Estacao::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->call('sortBy', 'senha')
        ->assertSet('sortField', 'site_id')
        ->assertSet('sortDirection', 'asc');
});

test('estacoes items per page can be changed', function () {
    Estacao::factory()->count(25)->create();

    Livewire::test(Index::class)
        ->assertSet('perPage', 10)
        ->assertSee('resultados')
        ->set('perPage', 25)
        ->assertSet('perPage', 25)
        ->assertDontSee('resultados');
});
