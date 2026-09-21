<?php

use App\Livewire\Clientes\Configuracoes;
use App\Livewire\Clientes\Create;
use App\Models\Cliente;
use App\Models\ClienteSegmento;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('configuracoes page shows configured segments', function () {
    ClienteSegmento::create(['nome' => 'Corporativo']);

    $this->get(route('clientes.configuracoes'))->assertOk();

    Livewire::test(Configuracoes::class)
        ->assertSee('Corporativo')
        ->assertSee('Novo segmento');
});

test('segmento can be created', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'Atacado')
        ->set('descricao', 'Venda em grande quantidade')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('cliente_segmentos', [
        'nome' => 'Atacado',
        'descricao' => 'Venda em grande quantidade',
        'ativo' => true,
    ]);
});

test('segmento creation requires nome', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('segmento cannot duplicate nome', function () {
    ClienteSegmento::create(['nome' => 'Corporativo']);

    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'Corporativo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('segmento can be edited', function () {
    $segmento = ClienteSegmento::create(['nome' => 'Corporativo']);

    Livewire::test(Configuracoes::class)
        ->call('abrirEdicao', $segmento->id)
        ->set('nome', 'Corporativo Premium')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($segmento->refresh()->nome)->toBe('Corporativo Premium');
});

test('segmento can be toggled', function () {
    $segmento = ClienteSegmento::create(['nome' => 'Corporativo', 'ativo' => true]);

    Livewire::test(Configuracoes::class)
        ->call('toggleAtivo', $segmento->id);

    expect($segmento->refresh()->ativo)->toBeFalse();
});

test('segmento can be deleted', function () {
    $segmento = ClienteSegmento::create(['nome' => 'Varejo']);

    Livewire::test(Configuracoes::class)
        ->call('destroy', $segmento->id);

    $this->assertDatabaseMissing('cliente_segmentos', ['id' => $segmento->id]);
});

test('create form only lists active configured segments', function () {
    ClienteSegmento::create(['nome' => 'Corporativo', 'ativo' => true]);
    ClienteSegmento::create(['nome' => 'Desativado', 'ativo' => false]);

    $component = Livewire::test(Create::class);

    expect($component->instance()->segmentos())->toContain('Corporativo')
        ->not->toContain('Desativado');
});

test('create form falls back to default segments when none configured', function () {
    expect(Livewire::test(Create::class)->instance()->segmentos())
        ->toBe(Cliente::SEGMENTOS);
});
