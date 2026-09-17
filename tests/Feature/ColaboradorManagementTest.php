<?php

use App\Livewire\Colaboradores\Create;
use App\Livewire\Colaboradores\Edit;
use App\Livewire\Colaboradores\Index;
use App\Livewire\Colaboradores\Show;
use App\Models\Colaborador;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('colaboradores index page is displayed', function () {
    Colaborador::factory()->count(3)->create();

    $this->get(route('colaboradores.index'))->assertOk();
});

test('colaboradores index shows list', function () {
    Colaborador::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Admissão')
        ->assertSee('Ações');
});

test('colaboradores index shows stats', function () {
    Colaborador::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de colaboradores')
        ->assertSee('Ativos')
        ->assertSee('Departamentos');
});

test('colaboradores filters can be cleared', function () {
    Colaborador::factory()->create(['nome' => 'João Silva', 'departamento' => 'TI']);
    Colaborador::factory()->create(['nome' => 'Maria Santos', 'departamento' => 'Financeiro']);

    Livewire::test(Index::class)
        ->set('search', 'João')
        ->set('filtroDepartamento', 'TI')
        ->set('filtroStatus', 'ativo')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroDepartamento', '')
        ->assertSet('filtroStatus', '');
});

test('colaboradores can be searched by name', function () {
    Colaborador::factory()->create(['nome' => 'João Silva']);
    Colaborador::factory()->create(['nome' => 'Maria Santos']);

    Livewire::test(Index::class)
        ->set('search', 'João')
        ->assertSee('João Silva')
        ->assertDontSee('Maria Santos');
});

test('colaboradores can be searched by email', function () {
    Colaborador::factory()->create(['email' => 'joao@test.com']);
    Colaborador::factory()->create(['email' => 'maria@test.com']);

    Livewire::test(Index::class)
        ->set('search', 'joao')
        ->assertSee('joao@test.com')
        ->assertDontSee('maria@test.com');
});

test('colaboradores can be filtered by department', function () {
    Colaborador::factory()->create(['departamento' => 'TI', 'nome' => 'Pessoa TI']);
    Colaborador::factory()->create(['departamento' => 'Financeiro', 'nome' => 'Pessoa Financeiro']);

    Livewire::test(Index::class)
        ->set('filtroDepartamento', 'TI')
        ->assertSee('Pessoa TI')
        ->assertDontSee('Pessoa Financeiro');
});

test('colaboradores can be filtered by status', function () {
    Colaborador::factory()->ativo()->create(['nome' => 'Ativo User']);
    Colaborador::factory()->inativo()->create(['nome' => 'Inativo User']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'ativo')
        ->assertSee('Ativo User')
        ->assertDontSee('Inativo User');
});

test('colaborador can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('cargo', 'Técnico')
        ->set('departamento', 'TI')
        ->set('categoria', 'CLT')
        ->set('data_admissao', '2026-01-15')
        ->set('salario', '5000')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colaboradores', [
        'nome' => 'João Silva',
        'email' => 'joao@test.com',
        'cpf' => '529.982.247-25',
        'categoria' => 'CLT',
    ]);
});

test('colaborador creation requires nome', function () {
    Livewire::test(Create::class)
        ->set('nome', '')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('colaborador creation requires valid email', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'invalid-email')
        ->set('cpf', '529.982.247-25')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('colaborador creation requires unique email', function () {
    Colaborador::factory()->create(['email' => 'existing@test.com']);

    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'existing@test.com')
        ->set('cpf', '529.982.247-25')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('colaborador creation requires unique cpf', function () {
    Colaborador::factory()->create(['cpf' => '529.982.247-25']);

    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->call('save')
        ->assertHasErrors(['cpf']);
});

test('colaborador creation requires categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('categoria', '')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('colaborador creation requires a valid categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('categoria', 'Estagiário')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('colaborador creation requires valid estado size', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('estado', 'SPX')
        ->call('save')
        ->assertHasErrors(['estado']);
});

test('colaborador creation rejects invalid cpf', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '123.456.789-00')
        ->call('save')
        ->assertHasErrors(['cpf']);
});

test('colaborador creation rejects invalid telefone', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('telefone', '119999')
        ->call('save')
        ->assertHasErrors(['telefone']);
});

test('colaborador creation rejects invalid cep', function () {
    Livewire::test(Create::class)
        ->set('nome', 'João Silva')
        ->set('email', 'joao@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('cep', '01234')
        ->call('save')
        ->assertHasErrors(['cep']);
});

test('colaborador cpf, telefone, cep and estado are masked', function () {
    Livewire::test(Create::class)
        ->set('cpf', '52998224725')
        ->assertSet('cpf', '529.982.247-25')
        ->set('telefone', '11987654321')
        ->assertSet('telefone', '(11) 98765-4321')
        ->set('cep', '01234567')
        ->assertSet('cep', '01234-567')
        ->set('estado', 'sp')
        ->assertSet('estado', 'SP');
});

test('colaborador can be edited', function () {
    $colaborador = Colaborador::factory()->create();

    Livewire::test(Edit::class, ['colaborador' => $colaborador])
        ->set('nome', 'Nome Atualizado')
        ->set('email', 'atualizado@test.com')
        ->set('cpf', '987.654.321-00')
        ->call('save')
        ->assertHasNoErrors();

    $colaborador->refresh();

    expect($colaborador->nome)->toEqual('Nome Atualizado');
    expect($colaborador->email)->toEqual('atualizado@test.com');
});

test('colaborador edit excludes own email from uniqueness check', function () {
    $colaborador = Colaborador::factory()->create(['email' => 'own@test.com']);

    Livewire::test(Edit::class, ['colaborador' => $colaborador])
        ->set('nome', 'Nome Atualizado')
        ->set('email', 'own@test.com')
        ->set('cpf', '987.654.321-00')
        ->call('save')
        ->assertHasNoErrors();
});

test('colaborador can be deleted', function () {
    $colaborador = Colaborador::factory()->create();

    Livewire::test(Show::class, ['colaborador' => $colaborador])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('colaboradores', ['id' => $colaborador->id]);
});

test('colaborador status can be toggled', function () {
    $colaborador = Colaborador::factory()->ativo()->create();

    Livewire::test(Index::class)
        ->call('toggleAtivo', $colaborador->id);

    $colaborador->refresh();
    expect($colaborador->ativo)->toBeFalse();
});

test('unauthenticated user cannot access colaboradores', function () {
    auth()->logout();

    $this->get(route('colaboradores.index'))->assertRedirect(route('login'));
    $this->get(route('colaboradores.create'))->assertRedirect(route('login'));
});

test('colaboradores page requires authentication', function () {
    $this->get(route('colaboradores.index'))->assertOk();
});

test('colaboradores with all fields can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Maria Santos')
        ->set('email', 'maria@test.com')
        ->set('cpf', '529.982.247-25')
        ->set('telefone', '(11) 99999-8888')
        ->set('cargo', 'Engenheira')
        ->set('departamento', 'Operações')
        ->set('categoria', 'Freelancer')
        ->set('data_admissao', '2025-06-01')
        ->set('salario', '8500.50')
        ->set('endereco', 'Rua das Flores, 123')
        ->set('cidade', 'São Paulo')
        ->set('estado', 'SP')
        ->set('cep', '01234-567')
        ->set('observacoes', 'Colaborador exemplar')
        ->set('ativo', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('colaboradores', [
        'nome' => 'Maria Santos',
        'telefone' => '(11) 99999-8888',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'categoria' => 'Freelancer',
    ]);
});
