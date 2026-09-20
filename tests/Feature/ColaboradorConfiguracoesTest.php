<?php

use App\Livewire\Colaboradores\Configuracoes;
use App\Livewire\Colaboradores\Create;
use App\Models\Colaborador;
use App\Models\ColaboradorCargo;
use App\Models\ColaboradorCategoria;
use App\Models\ColaboradorDepartamento;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('configuracoes page shows configured records', function () {
    ColaboradorCategoria::create(['nome' => 'CLT']);
    ColaboradorCargo::create(['nome' => 'Técnico']);
    ColaboradorDepartamento::create(['nome' => 'Operações']);

    $this->get(route('colaboradores.configuracoes'))->assertOk();

    Livewire::test(Configuracoes::class)
        ->assertSee('CLT')
        ->assertSee('Novo registro');
});

test('categoria can be created', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'PJ')
        ->set('descricao', 'Pessoa jurídica')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('colaborador_categorias', [
        'nome' => 'PJ',
        'descricao' => 'Pessoa jurídica',
        'ativo' => true,
    ]);
});

test('cargo can be created in cargos tab', function () {
    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'cargos')
        ->call('abrirNovo')
        ->set('nome', 'Engenheiro de Redes')
        ->call('salvar')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colaborador_cargos', ['nome' => 'Engenheiro de Redes']);
});

test('departamento can be created in departamentos tab', function () {
    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'departamentos')
        ->call('abrirNovo')
        ->set('nome', 'Financeiro')
        ->call('salvar')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colaborador_departamentos', ['nome' => 'Financeiro']);
});

test('configuration creation requires nome', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('configuration cannot duplicate nome within same tab', function () {
    ColaboradorCategoria::create(['nome' => 'CLT']);

    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'CLT')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('categoria can be edited', function () {
    $categoria = ColaboradorCategoria::create(['nome' => 'CLT']);

    Livewire::test(Configuracoes::class)
        ->call('abrirEdicao', $categoria->id)
        ->set('nome', 'CLT Efetivo')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($categoria->refresh()->nome)->toBe('CLT Efetivo');
});

test('cargo can be toggled', function () {
    $cargo = ColaboradorCargo::create(['nome' => 'Técnico', 'ativo' => true]);

    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'cargos')
        ->call('toggleAtivo', $cargo->id);

    expect($cargo->refresh()->ativo)->toBeFalse();
});

test('departamento can be deleted', function () {
    $departamento = ColaboradorDepartamento::create(['nome' => 'TI']);

    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'departamentos')
        ->call('destroy', $departamento->id);

    $this->assertDatabaseMissing('colaborador_departamentos', ['id' => $departamento->id]);
});

test('create form only lists active configured options', function () {
    ColaboradorCategoria::create(['nome' => 'CLT', 'ativo' => true]);
    ColaboradorCategoria::create(['nome' => 'Desativada', 'ativo' => false]);
    ColaboradorCargo::create(['nome' => 'Técnico', 'ativo' => true]);
    ColaboradorDepartamento::create(['nome' => 'TI', 'ativo' => true]);

    $component = Livewire::test(Create::class);

    expect($component->instance()->categorias)->toContain('CLT')
        ->not->toContain('Desativada');
    expect($component->instance()->cargos)->toContain('Técnico');
    expect($component->instance()->departamentos)->toContain('TI');
});

test('create form falls back to default categories when none configured', function () {
    expect(Livewire::test(Create::class)->instance()->categorias)
        ->toBe(Colaborador::CATEGORIAS);
});
