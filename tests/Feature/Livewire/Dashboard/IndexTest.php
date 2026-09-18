<?php

use App\Livewire\Dashboard\Index;
use App\Models\Estacao;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('dashboard renders successfully for authenticated user', function () {
    Livewire::test(Index::class)->assertOk();
});

test('dashboard shows user name and headings', function () {
    Livewire::test(Index::class)
        ->assertSee('Painel')
        ->assertSee($this->user->name);
});

test('dashboard shows empty state when no stations exist', function () {
    Livewire::test(Index::class)
        ->assertSee('Nenhuma estação registrada ainda');
});

test('dashboard shows network status breakdown when stations exist', function () {
    Estacao::factory()->count(5)->create(['status' => 'Ativo', 'situacao' => 'Em operação']);
    Estacao::factory()->count(3)->create(['status' => 'Aquisitado']);

    Livewire::test(Index::class)
        ->assertSee('Situação da rede')
        ->assertSee('Ativo')
        ->assertSee('Aquisitado')
        ->assertDontSee('Nenhuma estação registrada ainda');
});
