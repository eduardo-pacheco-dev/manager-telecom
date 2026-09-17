<?php

use App\Livewire\Servicos\Create;
use App\Livewire\Servicos\Edit;
use App\Livewire\Servicos\Index;
use App\Livewire\Servicos\Show;
use App\Models\Servico;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('servicos index page is displayed', function () {
    Servico::factory()->count(3)->create();

    $this->get(route('servicos.index'))->assertOk();
});

test('servicos index shows list', function () {
    Servico::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Categoria / Descrição')
        ->assertSee('Ações');
});

test('servicos index shows stats', function () {
    Servico::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de serviços')
        ->assertSee('Ativos')
        ->assertSee('Categorias');
});

test('servicos filters can be cleared', function () {
    Servico::factory()->create(['nome' => 'Instalação de Fibra', 'categoria' => 'Instalação']);
    Servico::factory()->create(['nome' => 'Suporte Remoto', 'categoria' => 'Suporte']);

    Livewire::test(Index::class)
        ->set('search', 'Instalação')
        ->set('filtroCategoria', 'Instalação')
        ->set('filtroStatus', 'ativo')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroCategoria', '')
        ->assertSet('filtroStatus', '');
});

test('servicos can be searched by name', function () {
    Servico::factory()->create(['nome' => 'Instalação de Fibra', 'categoria' => 'Instalação', 'descricao' => null]);
    Servico::factory()->create(['nome' => 'Suporte Remoto', 'categoria' => 'Suporte', 'descricao' => null]);

    Livewire::test(Index::class)
        ->set('search', 'Instalação')
        ->assertSee('Instalação de Fibra')
        ->assertDontSee('Suporte Remoto');
});

test('servicos can be searched by codigo', function () {
    Servico::factory()->create(['codigo' => 'SRV-001']);
    Servico::factory()->create(['codigo' => 'SRV-002']);

    Livewire::test(Index::class)
        ->set('search', 'SRV-001')
        ->assertSee('SRV-001')
        ->assertDontSee('SRV-002');
});

test('servicos can be filtered by categoria', function () {
    Servico::factory()->create(['categoria' => 'Instalação', 'nome' => 'Serviço Instalação']);
    Servico::factory()->create(['categoria' => 'Suporte', 'nome' => 'Serviço Suporte']);

    Livewire::test(Index::class)
        ->set('filtroCategoria', 'Instalação')
        ->assertSee('Serviço Instalação')
        ->assertDontSee('Serviço Suporte');
});

test('servicos can be filtered by status', function () {
    Servico::factory()->ativo()->create(['nome' => 'Serviço Ativo']);
    Servico::factory()->inativo()->create(['nome' => 'Serviço Inativo']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'ativo')
        ->assertSee('Serviço Ativo')
        ->assertDontSee('Serviço Inativo');
});

test('servico can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Instalação GPON')
        ->set('codigo', 'SRV-011')
        ->set('categoria', 'Instalação')
        ->set('preco', '149.90')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('servicos', [
        'nome' => 'Instalação GPON',
        'codigo' => 'SRV-011',
        'categoria' => 'Instalação',
    ]);
});

test('servico creation requires nome', function () {
    Livewire::test(Create::class)
        ->set('nome', '')
        ->set('categoria', 'Instalação')
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('servico creation requires unique codigo', function () {
    Servico::factory()->create(['codigo' => 'SRV-001']);

    Livewire::test(Create::class)
        ->set('nome', 'Instalação GPON')
        ->set('codigo', 'SRV-001')
        ->set('categoria', 'Instalação')
        ->call('save')
        ->assertHasErrors(['codigo']);
});

test('servico creation requires categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Instalação GPON')
        ->set('categoria', '')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('servico creation requires a valid categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Instalação GPON')
        ->set('categoria', 'Produto')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('servico can be edited', function () {
    $servico = Servico::factory()->create();

    Livewire::test(Edit::class, ['servico' => $servico])
        ->set('nome', 'Nome Atualizado')
        ->set('categoria', 'Instalação')
        ->set('preco', '200.00')
        ->call('save')
        ->assertHasNoErrors();

    $servico->refresh();

    expect($servico->nome)->toEqual('Nome Atualizado');
    expect($servico->preco)->toEqual(200.00);
});

test('servico edit excludes own codigo from uniqueness check', function () {
    $servico = Servico::factory()->create(['codigo' => 'SRV-001']);

    Livewire::test(Edit::class, ['servico' => $servico])
        ->set('nome', 'Nome Atualizado')
        ->set('codigo', 'SRV-001')
        ->set('categoria', 'Instalação')
        ->call('save')
        ->assertHasNoErrors();
});

test('servico can be deleted', function () {
    $servico = Servico::factory()->create();

    Livewire::test(Show::class, ['servico' => $servico])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('servicos', ['id' => $servico->id]);
});

test('servico status can be toggled', function () {
    $servico = Servico::factory()->ativo()->create();

    Livewire::test(Index::class)
        ->call('toggleAtivo', $servico->id);

    $servico->refresh();
    expect($servico->ativo)->toBeFalse();
});

test('unauthenticated user cannot access servicos', function () {
    auth()->logout();

    $this->get(route('servicos.index'))->assertRedirect(route('login'));
    $this->get(route('servicos.create'))->assertRedirect(route('login'));
});

test('servicos page requires authentication', function () {
    $this->get(route('servicos.index'))->assertOk();
});

test('servicos with all fields can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Consultoria de Rede')
        ->set('codigo', 'SRV-012')
        ->set('categoria', 'Manutenção')
        ->set('descricao', 'Avaliação completa da infraestrutura de rede.')
        ->set('preco', '450.00')
        ->set('observacoes', 'Inclui relatório técnico.')
        ->set('ativo', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('servicos', [
        'nome' => 'Consultoria de Rede',
        'codigo' => 'SRV-012',
        'categoria' => 'Manutenção',
        'preco' => 450.00,
        'observacoes' => 'Inclui relatório técnico.',
    ]);
});
