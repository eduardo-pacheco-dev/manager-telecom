<?php

use App\Livewire\Nokia\Create;
use App\Livewire\Nokia\Edit;
use App\Livewire\Nokia\Index;
use App\Livewire\Nokia\Show;
use App\Models\NokiaProjeto;
use App\Models\OrdemServico;
use App\Models\User;
use App\Services\ExcelExporter;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('nokia index page is displayed', function () {
    NokiaProjeto::factory()->count(3)->create();

    $this->get(route('nokia.index'))->assertOk();
});

test('nokia index shows list and stats', function () {
    NokiaProjeto::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee('Projetos Nokia')
        ->assertSee('Total de projetos')
        ->assertSee('Ativos')
        ->assertSee('OS vinculadas');
});

test('nokia projeto can be created', function () {
    Livewire::test(Create::class)
        ->set('nome', 'Implantação RAN TIM')
        ->set('status', 'Em andamento')
        ->set('data_inicio', '2026-05-01')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('nokia_projetos', [
        'codigo' => 'NOK-0001',
        'nome' => 'Implantação RAN TIM',
        'status' => 'Em andamento',
    ]);
});

test('nokia projeto creation requires nome', function () {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['nome']);
});

test('nokia projeto can be edited', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);

    Livewire::test(Edit::class, ['projeto' => $projeto])
        ->set('nome', 'Projeto Atualizado')
        ->set('status', 'Concluído')
        ->call('save')
        ->assertHasNoErrors();

    $projeto->refresh();

    expect($projeto->nome)->toBe('Projeto Atualizado');
    expect($projeto->status)->toBe('Concluído');
});

test('nokia projeto can be deleted and detaches orders', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Index::class)
        ->call('destroy', $projeto->id);

    $this->assertDatabaseMissing('nokia_projetos', ['id' => $projeto->id]);
    expect($ordem->refresh()->projeto_nokia_id)->toBeNull();
});

test('nokia show page displays vinculated orders', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    OrdemServico::factory()->count(2)->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->assertSee('NOK-0001')
        ->assertSee('Ordens de serviço vinculadas');
});

test('ordem can be vinculated to projeto', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create();

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('vincular', $ordem->id);

    expect($ordem->refresh()->projeto_nokia_id)->toBe($projeto->id);
});

test('ordem can be desvinculated from projeto', function () {
    $projeto = NokiaProjeto::factory()->create(['codigo' => 'NOK-0001']);
    $ordem = OrdemServico::factory()->create(['projeto_nokia_id' => $projeto->id]);

    Livewire::test(Show::class, ['projeto' => $projeto])
        ->call('desvincular', $ordem->id);

    expect($ordem->refresh()->projeto_nokia_id)->toBeNull();
});

test('nokia projetos can be selected for bulk deletion', function () {
    $p1 = NokiaProjeto::factory()->create();
    $p2 = NokiaProjeto::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $p1->id)
        ->call('alternarSelecao', $p2->id)
        ->assertSet('selecionados', [$p1->id, $p2->id])
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('nokia_projetos', ['id' => $p1->id]);
    $this->assertDatabaseMissing('nokia_projetos', ['id' => $p2->id]);
});

test('nokia projetos can be exported as excel', function () {
    NokiaProjeto::factory()->create(['codigo' => 'NOK-0001', 'nome' => 'Projeto Export']);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('unauthenticated user cannot access nokia', function () {
    auth()->logout();

    $this->get(route('nokia.index'))->assertRedirect(route('login'));
    $this->get(route('nokia.create'))->assertRedirect(route('login'));
});
