<?php

use App\Livewire\Estacoes\Configuracoes;
use App\Livewire\Estacoes\Create;
use App\Models\Estacao;
use App\Models\EstacaoDetentor;
use App\Models\EstacaoStatus;
use App\Models\EstacaoTecnologia;
use App\Models\EstacaoTipoConexao;
use App\Models\EstacaoTipoEv;
use App\Models\EstacaoTipoInfra;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('configuracoes page shows configured records', function () {
    EstacaoTecnologia::create(['nome' => 'LTE']);
    EstacaoTipoConexao::create(['nome' => 'Fibra Óptica']);
    EstacaoStatus::create(['nome' => 'Ativo']);
    EstacaoDetentor::create(['nome' => 'IHS BRAZIL']);
    EstacaoTipoInfra::create(['nome' => 'Greenfield']);
    EstacaoTipoEv::create(['nome' => 'POSTE']);

    $this->get(route('estacoes.configuracoes'))->assertOk();

    Livewire::test(Configuracoes::class)
        ->assertSee('LTE')
        ->assertSee('Novo registro');
});

test('tecnologia can be created', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', '6G')
        ->call('salvar')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('estacao_tecnologias', ['nome' => '6G']);
});

test('tipo conexao can be created in its tab', function () {
    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'tipos-conexao')
        ->call('abrirNovo')
        ->set('nome', 'Rádio')
        ->call('salvar')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('estacao_tipos_conexao', ['nome' => 'Rádio']);
});

test('detentor can be created in its tab', function () {
    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'detentores')
        ->call('abrirNovo')
        ->set('nome', 'NEW TOWER')
        ->call('salvar')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('estacao_detentores', ['nome' => 'NEW TOWER']);
});

test('configuration creation requires nome', function () {
    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('configuration cannot duplicate nome within same tab', function () {
    EstacaoTecnologia::create(['nome' => 'LTE']);

    Livewire::test(Configuracoes::class)
        ->call('abrirNovo')
        ->set('nome', 'LTE')
        ->call('salvar')
        ->assertHasErrors(['nome']);
});

test('configuration can be edited', function () {
    $tecnologia = EstacaoTecnologia::create(['nome' => 'LTE']);

    Livewire::test(Configuracoes::class)
        ->call('abrirEdicao', $tecnologia->id)
        ->set('nome', 'LTE Advanced')
        ->call('salvar')
        ->assertHasNoErrors();

    expect($tecnologia->refresh()->nome)->toBe('LTE Advanced');
});

test('configuration can be toggled', function () {
    $status = EstacaoStatus::create(['nome' => 'Ativo', 'ativo' => true]);

    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'status')
        ->call('toggleAtivo', $status->id);

    expect($status->refresh()->ativo)->toBeFalse();
});

test('configuration can be deleted', function () {
    $infra = EstacaoTipoInfra::create(['nome' => 'Rooftop']);

    Livewire::test(Configuracoes::class)
        ->call('mudarAba', 'tipos-infra')
        ->call('destroy', $infra->id);

    $this->assertDatabaseMissing('estacao_tipos_infra', ['id' => $infra->id]);
});

test('create form only lists active configured options', function () {
    EstacaoTecnologia::create(['nome' => 'LTE', 'ativo' => true]);
    EstacaoTecnologia::create(['nome' => '6G', 'ativo' => false]);

    $component = Livewire::test(Create::class);

    expect($component->instance()->tecnologias())->toContain('LTE')
        ->not->toContain('6G');
});

test('create form falls back to default values when none configured', function () {
    expect(Livewire::test(Create::class)->instance()->tecnologias())
        ->toBe(Estacao::TECNOLOGIAS);
});
