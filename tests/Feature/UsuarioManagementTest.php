<?php

use App\Livewire\Usuarios\Create;
use App\Livewire\Usuarios\Edit;
use App\Livewire\Usuarios\Index;
use App\Livewire\Usuarios\Show;
use App\Models\User;
use App\Services\ExcelExporter;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->actingAs($this->user);
});

test('usuarios index page is displayed', function () {
    User::factory()->count(3)->create();

    $this->get(route('usuarios.index'))->assertOk();
});

test('usuarios index shows list and stats', function () {
    User::factory()->count(4)->create();

    Livewire::test(Index::class)
        ->assertSee('Usuários')
        ->assertSee('Total de usuários')
        ->assertSee('Administradores')
        ->assertSee($this->user->name);
});

test('usuarios can be searched by name or email', function () {
    User::factory()->create(['name' => 'João Silva', 'email' => 'joao@test.com']);
    User::factory()->create(['name' => 'Maria Souza', 'email' => 'maria@test.com']);

    Livewire::test(Index::class)
        ->set('search', 'João')
        ->assertSee('João Silva')
        ->assertDontSee('Maria Souza');
});

test('usuarios can be filtered by role', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin Teste']);
    User::factory()->create(['name' => 'Usuário Comum']);

    Livewire::test(Index::class)
        ->set('filtroRole', 'admin')
        ->assertSee('Admin Teste')
        ->assertSee($this->user->name)
        ->assertDontSee('Usuário Comum');
});

test('usuarios can be filtered by status', function () {
    User::factory()->create(['name' => 'Ativo User']);
    User::factory()->inativo()->create(['name' => 'Inativo User']);

    Livewire::test(Index::class)
        ->set('filtroStatus', 'ativo')
        ->assertSee('Ativo User')
        ->assertDontSee('Inativo User');
});

test('usuario can be created', function () {
    Livewire::test(Create::class)
        ->set('name', 'Novo Usuário')
        ->set('email', 'novo@test.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'user')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'Novo Usuário',
        'email' => 'novo@test.com',
        'role' => 'user',
        'ativo' => true,
    ]);
});

test('usuario can be created as admin', function () {
    Livewire::test(Create::class)
        ->set('name', 'Admin Novo')
        ->set('email', 'admin2@test.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'admin')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'admin2@test.com',
        'role' => 'admin',
    ]);
});

test('usuario creation requires unique email', function () {
    User::factory()->create(['email' => 'existing@test.com']);

    Livewire::test(Create::class)
        ->set('name', 'Duplicado')
        ->set('email', 'existing@test.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('save')
        ->assertHasErrors(['email']);
});

test('usuario creation requires confirmed password', function () {
    Livewire::test(Create::class)
        ->set('name', 'Sem Confirmação')
        ->set('email', 'semconfirmacao@test.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'diferente')
        ->call('save')
        ->assertHasErrors(['password']);
});

test('usuario creation rejects invalid role', function () {
    Livewire::test(Create::class)
        ->set('name', 'Role Invalida')
        ->set('email', 'role@test.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'superuser')
        ->call('save')
        ->assertHasErrors(['role']);
});

test('usuario can be edited', function () {
    $usuario = User::factory()->create(['name' => 'Antigo Nome']);

    Livewire::test(Edit::class, ['usuario' => $usuario])
        ->set('name', 'Novo Nome')
        ->set('role', 'admin')
        ->call('save')
        ->assertHasNoErrors();

    $usuario->refresh();

    expect($usuario->name)->toBe('Novo Nome');
    expect($usuario->role)->toBe('admin');
});

test('usuario edit can change password', function () {
    $usuario = User::factory()->create();

    Livewire::test(Edit::class, ['usuario' => $usuario])
        ->set('password', 'novasenha123')
        ->set('password_confirmation', 'novasenha123')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('novasenha123', $usuario->refresh()->password))->toBeTrue();
});

test('usuario edit keeps password when blank', function () {
    $usuario = User::factory()->create();
    $senhaAtual = $usuario->password;

    Livewire::test(Edit::class, ['usuario' => $usuario])
        ->call('save')
        ->assertHasNoErrors();

    expect($usuario->refresh()->password)->toBe($senhaAtual);
});

test('usuario edit excludes own email from uniqueness check', function () {
    $usuario = User::factory()->create(['email' => 'own@test.com']);

    Livewire::test(Edit::class, ['usuario' => $usuario])
        ->set('email', 'own@test.com')
        ->call('save')
        ->assertHasNoErrors();
});

test('usuario show page is displayed', function () {
    $usuario = User::factory()->create(['name' => 'Usuário Show']);

    Livewire::test(Show::class, ['usuario' => $usuario])
        ->assertSee('Usuário Show')
        ->assertSee('Identificação')
        ->assertSee('Acesso');
});

test('usuario can be deleted', function () {
    $usuario = User::factory()->create();

    Livewire::test(Index::class)
        ->call('destroy', $usuario->id)
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
});

test('usuario cannot delete self', function () {
    Livewire::test(Index::class)
        ->call('destroy', $this->user->id);

    $this->assertDatabaseHas('users', ['id' => $this->user->id]);
});

test('usuario cannot deactivate self', function () {
    Livewire::test(Index::class)
        ->call('toggleAtivo', $this->user->id);

    expect($this->user->refresh()->ativo)->toBeTrue();
});

test('usuario status can be toggled', function () {
    $usuario = User::factory()->ativo()->create();

    Livewire::test(Index::class)
        ->call('toggleAtivo', $usuario->id);

    expect($usuario->refresh()->ativo)->toBeFalse();
});

test('usuarios can be selected for bulk deletion', function () {
    $u1 = User::factory()->create();
    $u2 = User::factory()->create();

    Livewire::test(Index::class)
        ->call('alternarSelecao', $u1->id)
        ->call('alternarSelecao', $u2->id)
        ->assertSet('selecionados', [$u1->id, $u2->id])
        ->call('excluirSelecionados')
        ->assertSet('selecionados', []);

    $this->assertDatabaseMissing('users', ['id' => $u1->id]);
    $this->assertDatabaseMissing('users', ['id' => $u2->id]);
});

test('usuarios can select all from current page', function () {
    User::factory()->count(12)->create();

    $component = Livewire::test(Index::class);
    $idsPagina = $component->instance()->usuarios()->pluck('id')->all();

    $component->call('selecionarTodosDaPagina');

    expect($component->instance()->selecionados)->toBe(array_values($idsPagina));
});

test('usuarios can be exported as excel', function () {
    User::factory()->create(['name' => 'Export User', 'email' => 'export@test.com']);

    $response = Livewire::test(Index::class)
        ->call('exportarTodos');

    $response->assertStatus(200);
    expect($response->instance()->exportarTodos(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('selected usuarios can be exported as excel', function () {
    $u1 = User::factory()->create(['name' => 'AAA']);
    $u2 = User::factory()->create(['name' => 'BBB']);

    $component = Livewire::test(Index::class)
        ->call('alternarSelecao', $u1->id)
        ->call('alternarSelecao', $u2->id);

    $response = $component->call('exportarSelecionados');

    expect($response->instance()->exportarSelecionados(app(ExcelExporter::class)))
        ->toBeInstanceOf(StreamedResponse::class);
});

test('export selected usuarios requires selection', function () {
    Livewire::test(Index::class)
        ->call('exportarSelecionados')
        ->assertStatus(422);
});

test('unauthenticated user cannot access usuarios', function () {
    auth()->logout();

    $this->get(route('usuarios.index'))->assertRedirect(route('login'));
    $this->get(route('usuarios.create'))->assertRedirect(route('login'));
});
