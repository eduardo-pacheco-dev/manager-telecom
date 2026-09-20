<?php

use App\Livewire\OrdensServico\Tipos;
use App\Models\OrdemServicoTipo;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('settings page shows service order types', function () {
    OrdemServicoTipo::create(['nome' => 'Manutenção']);

    $this->get(route('ordens-servico.tipos'))->assertOk();

    Livewire::test(Tipos::class)
        ->assertSee('Manutenção')
        ->assertSee('Novo tipo');
});

test('service order type can be created', function () {
    Livewire::test(Tipos::class)
        ->call('abrirNovo')
        ->set('nome', 'Comissionamento')
        ->set('descricao', 'Teste de comissionamento')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('ordem_servico_tipos', [
        'nome' => 'Comissionamento',
        'descricao' => 'Teste de comissionamento',
        'ativo' => true,
    ]);
});

test('service order type creation requires nome', function () {
    Livewire::test(Tipos::class)
        ->call('abrirNovo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('service order type can be edited', function () {
    $tipo = OrdemServicoTipo::create(['nome' => 'Manutenção']);

    Livewire::test(Tipos::class)
        ->call('abrirEdicao', $tipo->id)
        ->set('nome', 'Manutenção Corretiva')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($tipo->refresh()->nome)->toBe('Manutenção Corretiva');
});

test('service order type can be toggled', function () {
    $tipo = OrdemServicoTipo::create(['nome' => 'Manutenção', 'ativo' => true]);

    Livewire::test(Tipos::class)
        ->call('toggleAtivo', $tipo->id);

    expect($tipo->refresh()->ativo)->toBeFalse();
});

test('service order type can be deleted', function () {
    $tipo = OrdemServicoTipo::create(['nome' => 'Manutenção']);

    Livewire::test(Tipos::class)
        ->call('destroy', $tipo->id);

    $this->assertDatabaseMissing('ordem_servico_tipos', ['id' => $tipo->id]);
});

test('service order types feed the create form', function () {
    OrdemServicoTipo::create(['nome' => 'Comissionamento', 'ativo' => true]);
    OrdemServicoTipo::create(['nome' => 'Desativado', 'ativo' => false]);

    $tipos = \App\Models\OrdemServico::tiposDisponiveis();

    expect($tipos)->toContain('Comissionamento')
        ->not->toContain('Desativado');
});