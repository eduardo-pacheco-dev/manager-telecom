<?php

use App\Livewire\Servicos\Configuracoes;
use App\Livewire\Servicos\Create;
use App\Models\Servico;
use App\Models\ServicoCategoria;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('configuracoes page shows configured categories', function () {
    ServicoCategoria::create(['nome' => 'Instalação']);

    $this->get(route('servicos.configuracoes'))->assertOk();

    Livewire::test(Configuracoes::class)
        ->assertSee('Instalação')
        ->assertSee('Nova categoria');
});

test('categoria can be created', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'Corretiva')
        ->set('descricao', 'Reparo de falhas')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('servico_categorias', [
        'nome' => 'Corretiva',
        'descricao' => 'Reparo de falhas',
        'ativo' => true,
    ]);
});

test('categoria creation requires nome', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('categoria cannot duplicate nome', function () {
    ServicoCategoria::create(['nome' => 'Suporte']);

    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'Suporte')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('categoria can be edited', function () {
    $categoria = ServicoCategoria::create(['nome' => 'Suporte']);

    Livewire::test(Configuracoes::class)
        ->call('abrirEdicao', $categoria->id)
        ->set('nome', 'Suporte N1')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($categoria->refresh()->nome)->toBe('Suporte N1');
});

test('categoria can be toggled', function () {
    $categoria = ServicoCategoria::create(['nome' => 'Suporte', 'ativo' => true]);

    Livewire::test(Configuracoes::class)
        ->call('toggleAtivo', $categoria->id);

    expect($categoria->refresh()->ativo)->toBeFalse();
});

test('categoria can be deleted', function () {
    $categoria = ServicoCategoria::create(['nome' => 'Configuração']);

    Livewire::test(Configuracoes::class)
        ->call('destroy', $categoria->id);

    $this->assertDatabaseMissing('servico_categorias', ['id' => $categoria->id]);
});

test('create form only lists active configured categories', function () {
    ServicoCategoria::create(['nome' => 'Instalação', 'ativo' => true]);
    ServicoCategoria::create(['nome' => 'Desativada', 'ativo' => false]);

    $component = Livewire::test(Create::class);

    expect($component->instance()->categorias())->toContain('Instalação')
        ->not->toContain('Desativada');
});

test('create form falls back to default categories when none configured', function () {
    expect(Livewire::test(Create::class)->instance()->categorias())
        ->toBe(Servico::CATEGORIAS);
});
