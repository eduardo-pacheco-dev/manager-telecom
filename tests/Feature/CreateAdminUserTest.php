<?php

use App\Console\Commands\CreateAdminUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user:admin creates an admin user', function () {
    $this->artisan(CreateAdminUser::class, [
        '--name' => 'Administrador',
        '--email' => 'admin@test.com',
        '--password' => 'senha12345',
    ])->assertSuccessful();

    $user = User::where('email', 'admin@test.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe('admin')
        ->and($user->ativo)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('senha12345', $user->password))->toBeTrue();
});

test('user:admin is idempotent for existing email', function () {
    User::factory()->create(['email' => 'admin@test.com', 'role' => 'user']);

    $this->artisan(CreateAdminUser::class, [
        '--name' => 'Administrador',
        '--email' => 'admin@test.com',
        '--password' => 'senha12345',
    ])->assertSuccessful();

    $this->assertDatabaseCount('users', 1);

    expect(User::where('email', 'admin@test.com')->first()->role)->toBe('admin');
});

test('user:admin rejects weak password', function () {
    $this->artisan(CreateAdminUser::class, [
        '--name' => 'Administrador',
        '--email' => 'admin@test.com',
        '--password' => '123',
    ])->assertFailed();

    $this->assertDatabaseMissing('users', ['email' => 'admin@test.com']);
});
