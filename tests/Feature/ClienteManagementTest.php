<?php

use App\Livewire\Clientes\Create;
use App\Livewire\Clientes\Edit;
use App\Livewire\Clientes\Index;
use App\Livewire\Clientes\Show;
use App\Models\Cliente;
use App\Models\User;
use App\Services\ExcelExporter;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('clientes index page is displayed', function () {
    Cliente::factory()->count(3)->create();

    $this->get(route('clientes.index'))->assertOk();
});

test('clientes index shows list', function () {
    Cliente::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('CNPJ')
        ->assertSee('Ações');
});

test('clientes index shows stats', function () {
    Cliente::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de clientes')
        ->assertSee('Ativos')
        ->assertSee('Segmentos');
});

test('cliente can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Empresa Teste Ltda')
        ->set('email', 'empresa@teste.com')
        ->set('documento', '12.345.678/0001-95')
        ->set('segmento', 'Corporativo')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('clientes', [
        'nome' => 'Empresa Teste Ltda',
        'email' => 'empresa@teste.com',
        'documento' => '12.345.678/0001-95',
        'segmento' => 'Corporativo',
    ]);
});

test('cliente creation requires nome and email', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['nome', 'email', 'documento']);
});

test('cliente creation rejects invalid cnpj', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Empresa Teste')
        ->set('email', 'empresa@teste.com')
        ->set('documento', '00.000.000/0000-00')
        ->call('save')
        ->assertHasErrors(['documento']);
});

test('cliente creation requires unique email and cnpj', function () {
    Cliente::factory()->create(['email' => 'empresa@teste.com', 'documento' => '12.345.678/0001-95']);

    Livewire::test(Create::class)
        ->set('nome', 'Outra Empresa')
        ->set('email', 'empresa@teste.com')
        ->set('documento', '12.345.678/0001-95')
        ->call('save')
        ->assertHasErrors(['email', 'documento']);
});

test('cliente cnpj is masked', function () {
    Livewire::test(Create::class)
        ->set('documento', '12345678000195')
        ->assertSet('documento', '12.345.678/0001-95');
});

test('cliente can be edited', function () {
    $cliente = Cliente::factory()->create();

    Livewire::test(Edit::class, ['cliente' => $cliente])
        ->set('nome', 'Nome Atualizado')
        ->call('save')
        ->assertHasNoErrors();

    expect($cliente->refresh()->nome)->toBe('Nome Atualizado');
});

test('cliente can be shown', function () {
    $cliente = Cliente::factory()->create(['nome' => 'Empresa Exemplo']);

    $this->get(route('clientes.show', $cliente))->assertOk();

    Livewire::test(Show::class, ['cliente' => $cliente])
        ->assertSee('Empresa Exemplo')
        ->assertSee('CNPJ');
});

test('cliente can be deleted', function () {
    $cliente = Cliente::factory()->create();

    Livewire::test(Show::class, ['cliente' => $cliente])
        ->call('destroy')
        ->assertRedirect(route('clientes.index'));

    $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
});

test('cliente can be toggled', function () {
    $cliente = Cliente::factory()->create(['ativo' => true]);

    Livewire::test(Show::class, ['cliente' => $cliente])
        ->call('toggleAtivo');

    expect($cliente->refresh()->ativo)->toBeFalse();
});

test('clientes can be searched', function () {
    Cliente::factory()->create(['nome' => 'Empresa Alfa']);
    Cliente::factory()->create(['nome' => 'Empresa Beta']);

    Livewire::test(Index::class)
        ->set('search', 'Alfa')
        ->assertSee('Empresa Alfa')
        ->assertDontSee('Empresa Beta');
});

test('clientes can be filtered by segment', function () {
    $corp = Cliente::factory()->create(['segmento' => 'Corporativo']);
    Cliente::factory()->create(['segmento' => 'Varejo']);

    Livewire::test(Index::class)
        ->set('filtroSegmento', 'Corporativo')
        ->assertSee($corp->nome);
});

test('clientes can be selected for bulk actions', function () {
    $c1 = Cliente::factory()->create(['ativo' => true]);
    $c2 = Cliente::factory()->create(['ativo' => true]);

    Livewire::test(Index::class)
        ->call('alternarSelecao', $c1->id)
        ->call('alternarSelecao', $c2->id)
        ->assertSet('selecionados', [$c1->id, $c2->id])
        ->call('desativarSelecionados')
        ->assertSet('selecionados', []);

    expect($c1->refresh()->ativo)->toBeFalse();
    expect($c2->refresh()->ativo)->toBeFalse();
});

test('clientes can be bulk activated', function () {
    $c1 = Cliente::factory()->create(['ativo' => false]);
    $c2 = Cliente::factory()->create(['ativo' => false]);

    Livewire::test(Index::class)
        ->call('alternarSelecao', $c1->id)
        ->call('alternarSelecao', $c2->id)
        ->call('ativarSelecionados')
        ->assertSet('selecionados', []);

    expect($c1->refresh()->ativo)->toBeTrue();
    expect($c2->refresh()->ativo)->toBeTrue();
});

test('clientes can be bulk deleted', function () {
    $c1 = Cliente::factory()->create();
    $c2 = Cliente::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $c1->id)
        ->call('alternarSelecao', $c2->id)
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('clientes', ['id' => $c1->id]);
    $this->assertDatabaseMissing('clientes', ['id' => $c2->id]);
});

test('clientes can select all from current page', function () {
    Cliente::factory()->count(12)->create();

    $component = Livewire::test(Index::class);
    $idsPagina = $component->instance()->clientes()->pluck('id')->all();

    $component->call('selecionarTodosDaPagina');

    expect($component->instance()->selecionados)->toBe(array_values($idsPagina));
});

test('clientes can be exported as excel', function () {
    Cliente::factory()->create([
        'nome' => 'Empresa Export',
        'email' => 'export@teste.com',
        'documento' => '12.345.678/0001-95',
        'ativo' => true,
    ]);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('selected clientes can be exported as excel', function () {
    $c1 = Cliente::factory()->create(['nome' => 'Empresa A']);
    $c2 = Cliente::factory()->create(['nome' => 'Empresa B']);

    $component = Livewire::test(Index::class)
        ->call('alternarSelecao', $c1->id)
        ->call('alternarSelecao', $c2->id);

    $response = $component->call('exportarSelecionados');

    expect($response->instance()->exportarSelecionados(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('export selected requires selection', function () {
    Livewire::test(Index::class)
        ->call('exportarSelecionados')
        ->assertStatus(422);
});
