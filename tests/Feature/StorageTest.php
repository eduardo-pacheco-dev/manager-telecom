<?php

use App\Livewire\Storage\Index;
use App\Models\Estacao;
use App\Models\EstacaoAnexo;
use App\Models\OrdemServico;
use App\Models\RadioLink;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('storage index page is displayed', function () {
    Estacao::factory()->count(3)->create();

    $this->get(route('storage.index'))->assertOk();
});

test('storage index shows stats and stations', function () {
    Estacao::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Armazenamento')
        ->assertSee('arquivo(s)')
        ->assertSee('estação(ões)');
});

test('storage index shows station files', function () {
    $estacao = Estacao::factory()->create();
    $estacao->anexos()->create([
        'nome' => 'contrato.pdf',
        'arquivo' => 'anexos/estacao/'.$estacao->id.'/contrato.pdf',
        'mime' => 'application/pdf',
        'tamanho' => 2048,
    ]);

    Livewire::test(Index::class)
        ->assertSee($estacao->site_id)
        ->call('abrirEstacao', $estacao->id)
        ->assertSee('contrato.pdf');
});

test('storage index shows related service orders with files', function () {
    $estacao = Estacao::factory()->create();
    $os = OrdemServico::factory()->create(['estacao_a_id' => $estacao->id]);
    $os->anexos()->create([
        'nome' => 'laudo.pdf',
        'arquivo' => 'anexos/ordem-servico/'.$os->id.'/laudo.pdf',
        'mime' => 'application/pdf',
        'tamanho' => 1024,
    ]);

    Livewire::test(Index::class)
        ->assertSee($estacao->site_id)
        ->call('abrirEstacao', $estacao->id)
        ->assertSee($os->codigo)
        ->call('abrirOrdem', $os->id)
        ->assertSee('laudo.pdf');
});

test('storage index shows related radio link files', function () {
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();
    $radioLink = RadioLink::factory()->create([
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
    ]);
    $radioLink->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/radio-link/'.$radioLink->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Index::class)
        ->assertSee($estacaoA->site_id)
        ->call('abrirEstacao', $estacaoA->id)
        ->assertSee($radioLink->codigo)
        ->call('abrirRadioLink', $radioLink->id)
        ->assertSee('medicao.png');
});

test('storage search filters stations', function () {
    Estacao::factory()->create(['site_id' => 'ABC123']);
    Estacao::factory()->create(['site_id' => 'XYZ789']);

    Livewire::test(Index::class)
        ->set('search', 'ABC')
        ->assertSee('ABC123')
        ->assertDontSee('XYZ789');
});

test('storage tree shows stations and can be expanded', function () {
    $estacao = Estacao::factory()->create();
    $os = OrdemServico::factory()->create(['estacao_a_id' => $estacao->id]);

    Livewire::test(Index::class)
        ->assertSee('Árvore de arquivos')
        ->assertSee($estacao->site_id)
        ->call('alternarExpandido', 'estacao-'.$estacao->id)
        ->assertSet('expandidos', ['estacao-'.$estacao->id])
        ->assertSee($os->codigo);
});

test('storage tree toggle hides the sidebar', function () {
    Estacao::factory()->create();

    Livewire::test(Index::class)
        ->assertSet('showArvore', true)
        ->call('alternarArvore')
        ->assertSet('showArvore', false)
        ->assertDontSee('Árvore de arquivos');
});

test('storage grid and list views can be toggled', function () {
    Estacao::factory()->create();

    Livewire::test(Index::class)
        ->assertSet('view', 'lista')
        ->call('alternarView')
        ->assertSet('view', 'grade')
        ->call('alternarView')
        ->assertSet('view', 'lista');
});

test('file can be uploaded to a station from storage', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();

    Livewire::test(Index::class)
        ->call('abrirUploadEstacao', $estacao->id)
        ->set('arquivo', UploadedFile::fake()->create('contrato.pdf', 2048, 'application/pdf'))
        ->call('salvarArquivo')
        ->assertHasNoErrors()
        ->assertSet('showUploadModal', false);

    $this->assertDatabaseHas('estacao_anexos', [
        'estacao_id' => $estacao->id,
        'nome' => 'contrato.pdf',
        'mime' => 'application/pdf',
    ]);

    $anexo = EstacaoAnexo::where('estacao_id', $estacao->id)->first();

    expect($anexo)->not->toBeNull();
    Storage::disk('local')->assertExists($anexo->arquivo);
});

test('file can be uploaded to a service order from storage', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();
    $os = OrdemServico::factory()->create(['estacao_a_id' => $estacao->id]);

    Livewire::test(Index::class)
        ->call('abrirUploadOrdem', $os->id)
        ->set('arquivo', UploadedFile::fake()->create('laudo.pdf', 1024, 'application/pdf'))
        ->call('salvarArquivo')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ordem_servico_anexos', [
        'ordem_servico_id' => $os->id,
        'nome' => 'laudo.pdf',
    ]);
});

test('upload requires a file', function () {
    Livewire::test(Index::class)
        ->call('abrirUploadEstacao', Estacao::factory()->create()->id)
        ->call('salvarArquivo')
        ->assertHasErrors(['arquivo']);
});

test('station anexo can be removed from storage', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();
    $anexo = $estacao->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/estacao/'.$estacao->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Index::class)
        ->call('removerAnexoEstacao', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('estacao_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('service order anexo can be removed from storage', function () {
    Storage::fake('local');
    $estacao = Estacao::factory()->create();
    $os = OrdemServico::factory()->create(['estacao_a_id' => $estacao->id]);
    $anexo = $os->anexos()->create([
        'nome' => 'laudo.pdf',
        'arquivo' => 'anexos/ordem-servico/'.$os->id.'/laudo.pdf',
        'mime' => 'application/pdf',
        'tamanho' => 1024,
    ]);

    Livewire::test(Index::class)
        ->call('removerAnexoOrdem', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('ordem_servico_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('radio link anexo can be removed from storage', function () {
    Storage::fake('local');
    $estacaoA = Estacao::factory()->create();
    $estacaoB = Estacao::factory()->create();
    $radioLink = RadioLink::factory()->create([
        'estacao_a_id' => $estacaoA->id,
        'estacao_b_id' => $estacaoB->id,
    ]);
    $anexo = $radioLink->anexos()->create([
        'nome' => 'medicao.png',
        'arquivo' => 'anexos/radio-link/'.$radioLink->id.'/medicao.png',
        'mime' => 'image/png',
        'tamanho' => 1024,
    ]);

    Livewire::test(Index::class)
        ->call('removerAnexoRadioLink', $anexo->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('radio_link_anexos', ['id' => $anexo->id]);
    Storage::disk('local')->assertMissing($anexo->arquivo);
});

test('storage stats count all files', function () {
    $estacao = Estacao::factory()->create();
    $os = OrdemServico::factory()->create(['estacao_a_id' => $estacao->id]);
    $radioLink = RadioLink::factory()->create([
        'estacao_a_id' => $estacao->id,
        'estacao_b_id' => Estacao::factory()->create()->id,
    ]);

    $estacao->anexos()->create(['nome' => 'a.pdf', 'arquivo' => 'a.pdf', 'mime' => 'application/pdf', 'tamanho' => 10]);
    $os->anexos()->create(['nome' => 'b.pdf', 'arquivo' => 'b.pdf', 'mime' => 'application/pdf', 'tamanho' => 20]);
    $radioLink->anexos()->create(['nome' => 'c.png', 'arquivo' => 'c.png', 'mime' => 'image/png', 'tamanho' => 30]);

    Livewire::test(Index::class)
        ->assertSet('stats.arquivos', 3)
        ->assertSet('stats.tamanho', 60);
});
