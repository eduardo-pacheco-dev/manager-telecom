<?php

use App\Livewire\Servicos\Create;
use App\Livewire\Servicos\Edit;
use App\Livewire\Servicos\Index;
use App\Livewire\Servicos\Show;
use App\Models\Servico;
use App\Models\User;
use App\Services\ExcelExporter;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

test('servicos can be selected for bulk actions', function () {
    $s1 = Servico::factory()->create(['ativo' => true]);
    $s2 = Servico::factory()->create(['ativo' => true]);

    Livewire::test(Index::class)
        ->call('alternarSelecao', $s1->id)
        ->call('alternarSelecao', $s2->id)
        ->assertSet('selecionados', [$s1->id, $s2->id])
        ->call('desativarSelecionados')
        ->assertSet('selecionados', []);

    expect($s1->refresh()->ativo)->toBeFalse();
    expect($s2->refresh()->ativo)->toBeFalse();
});

test('servicos can be bulk activated', function () {
    $s1 = Servico::factory()->create(['ativo' => false]);
    $s2 = Servico::factory()->create(['ativo' => false]);

    Livewire::test(Index::class)
        ->call('alternarSelecao', $s1->id)
        ->call('alternarSelecao', $s2->id)
        ->call('ativarSelecionados')
        ->assertSet('selecionados', []);

    expect($s1->refresh()->ativo)->toBeTrue();
    expect($s2->refresh()->ativo)->toBeTrue();
});

test('servicos can be bulk deleted', function () {
    $s1 = Servico::factory()->create();
    $s2 = Servico::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $s1->id)
        ->call('alternarSelecao', $s2->id)
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('servicos', ['id' => $s1->id]);
    $this->assertDatabaseMissing('servicos', ['id' => $s2->id]);
});

test('servicos can select all from current page', function () {
    Servico::factory()->count(12)->create();

    $component = Livewire::test(Index::class);
    $idsPagina = $component->instance()->servicos()->pluck('id')->all();

    $component->call('selecionarTodosDaPagina');

    expect($component->instance()->selecionados)->toBe(array_values($idsPagina));
});

test('servicos can be exported as excel', function () {
    Servico::factory()->create([
        'nome' => 'Serviço Export',
        'codigo' => 'SRV-EXPORT',
        'categoria' => 'Suporte',
        'ativo' => true,
    ]);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('selected servicos can be exported as excel', function () {
    $s1 = Servico::factory()->create(['nome' => 'Serviço A']);
    $s2 = Servico::factory()->create(['nome' => 'Serviço B']);

    $component = Livewire::test(Index::class)
        ->call('alternarSelecao', $s1->id)
        ->call('alternarSelecao', $s2->id);

    $response = $component->call('exportarSelecionados');

    expect($response->instance()->exportarSelecionados(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('export selected requires selection', function () {
    Livewire::test(Index::class)
        ->call('exportarSelecionados')
        ->assertStatus(422);
});

test('servico codigo is generated automatically', function () {
    Livewire::test(Create::class)
        ->assertSet('codigo', 'SRV-0001');
});

test('servico codigo increments sequentially', function () {
    Servico::factory()->create(['codigo' => 'SRV-0003']);

    Livewire::test(Create::class)
        ->assertSet('codigo', 'SRV-0004');
});
