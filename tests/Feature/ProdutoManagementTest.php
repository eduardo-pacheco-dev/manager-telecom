<?php

use App\Livewire\Produtos\Create;
use App\Livewire\Produtos\Edit;
use App\Livewire\Produtos\Index;
use App\Livewire\Produtos\Show;
use App\Models\Produto;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('produtos index page is displayed', function () {
    Produto::factory()->count(3)->create();

    $this->get(route('produtos.index'))->assertOk();
});

test('produtos index shows list', function () {
    Produto::factory()->count(5)->create();

    Livewire::test(Index::class)
        ->assertSee('Categoria / Descrição')
        ->assertSee('Ações');
});

test('produtos index shows stats', function () {
    Produto::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Total de produtos')
        ->assertSee('Ativos')
        ->assertSee('Categorias');
});

test('produtos filters can be cleared', function () {
    Produto::factory()->create(['nome' => 'Roteador Wi-Fi', 'categoria' => 'Equipamento']);
    Produto::factory()->create(['nome' => 'Adaptador de Energia', 'categoria' => 'Acessório']);

    Livewire::test(Index::class)
        ->set('search', 'Roteador')
        ->set('filtroCategoria', 'Equipamento')
        ->set('filtroStatus', 'ativo')
        ->assertSee('Limpar filtros')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('filtroCategoria', '')
        ->assertSet('filtroStatus', '');
});

test('produtos can be searched by name', function () {
    Produto::factory()->create(['nome' => 'Roteador Wi-Fi', 'categoria' => 'Equipamento', 'descricao' => null]);
    Produto::factory()->create(['nome' => 'Adaptador de Energia', 'categoria' => 'Acessório', 'descricao' => null]);

    Livewire::test(Index::class)
        ->set('search', 'Roteador')
        ->assertSee('Roteador Wi-Fi')
        ->assertDontSee('Adaptador de Energia');
});

test('produtos can be searched by codigo', function () {
    Produto::factory()->create(['codigo' => 'PROD-001']);
    Produto::factory()->create(['codigo' => 'PROD-002']);

    Livewire::test(Index::class)
        ->set('search', 'PROD-001')
        ->assertSee('PROD-001')
        ->assertDontSee('PROD-002');
});

test('produtos can be filtered by categoria', function () {
    Produto::factory()->create(['categoria' => 'Equipamento', 'nome' => 'Produto Equipamento']);
    Produto::factory()->create(['categoria' => 'Acessório', 'nome' => 'Produto Acessório']);

    Livewire::test(Index::class)
        ->set('filtroCategoria', 'Equipamento')
        ->assertSee('Produto Equipamento')
        ->assertDontSee('Produto Acessório');
});

test('produtos can be filtered by status', function () {
    Produto::factory()->ativo()->create(['nome' => 'Produto Ativo']);
    Produto::factory()->inativo()->create(['nome' => 'Produto Inativo']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'ativo')
        ->assertSee('Produto Ativo')
        ->assertDontSee('Produto Inativo');
});

test('produto can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Roteador Wi-Fi 6')
        ->set('codigo', 'PROD-011')
        ->set('categoria', 'Equipamento')
        ->set('preco', '199.90')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('produtos', [
        'nome' => 'Roteador Wi-Fi 6',
        'codigo' => 'PROD-011',
        'categoria' => 'Equipamento',
    ]);
});

test('produto creation requires nome', function () {
    Livewire::test(Create::class)
        ->set('nome', '')
        ->set('categoria', 'Equipamento')
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('produto creation requires unique codigo', function () {
    Produto::factory()->create(['codigo' => 'PROD-001']);

    Livewire::test(Create::class)
        ->set('nome', 'Roteador Wi-Fi 6')
        ->set('codigo', 'PROD-001')
        ->set('categoria', 'Equipamento')
        ->call('save')
        ->assertHasErrors(['codigo']);
});

test('produto creation requires categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Roteador Wi-Fi 6')
        ->set('categoria', '')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('produto creation requires a valid categoria', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Roteador Wi-Fi 6')
        ->set('categoria', 'Serviço')
        ->call('save')
        ->assertHasErrors(['categoria']);
});

test('produto can be edited', function () {
    $produto = Produto::factory()->create();

    Livewire::test(Edit::class, ['produto' => $produto])
        ->set('nome', 'Nome Atualizado')
        ->set('categoria', 'Equipamento')
        ->set('preco', '250.00')
        ->call('save')
        ->assertHasNoErrors();

    $produto->refresh();

    expect($produto->nome)->toEqual('Nome Atualizado');
    expect($produto->preco)->toEqual(250.00);
});

test('produto edit excludes own codigo from uniqueness check', function () {
    $produto = Produto::factory()->create(['codigo' => 'PROD-001']);

    Livewire::test(Edit::class, ['produto' => $produto])
        ->set('nome', 'Nome Atualizado')
        ->set('codigo', 'PROD-001')
        ->set('categoria', 'Equipamento')
        ->call('save')
        ->assertHasNoErrors();
});

test('produto can be deleted', function () {
    $produto = Produto::factory()->create();

    Livewire::test(Show::class, ['produto' => $produto])
        ->call('destroy')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
});

test('produto status can be toggled', function () {
    $produto = Produto::factory()->ativo()->create();

    Livewire::test(Index::class)
        ->call('toggleAtivo', $produto->id);

    $produto->refresh();
    expect($produto->ativo)->toBeFalse();
});

test('unauthenticated user cannot access produtos', function () {
    auth()->logout();

    $this->get(route('produtos.index'))->assertRedirect(route('login'));
    $this->get(route('produtos.create'))->assertRedirect(route('login'));
});

test('produtos page requires authentication', function () {
    $this->get(route('produtos.index'))->assertOk();
});

test('produtos with all fields can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Caixa de Emenda Óptica')
        ->set('codigo', 'PROD-012')
        ->set('categoria', 'Infraestrutura')
        ->set('descricao', 'Caixa para fusão de fibras em campo.')
        ->set('preco', '85.50')
        ->set('observacoes', 'Uso em instalações externas.')
        ->set('ativo', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('produtos', [
        'nome' => 'Caixa de Emenda Óptica',
        'codigo' => 'PROD-012',
        'categoria' => 'Infraestrutura',
        'preco' => 85.50,
        'observacoes' => 'Uso em instalações externas.',
    ]);
});
