<?php

use App\Livewire\Settings\Language;
use App\Models\User;
use Livewire\Livewire;

test('language settings page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/language')->assertOk();
});

test('the session locale is applied to the rendered page', function () {
    $this->actingAs(User::factory()->create());

    session(['locale' => 'en']);

    $this->get('/settings/language')
        ->assertOk()
        ->assertSee('Language settings');
});

test('language can be updated and stored in the session', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Language::class)
        ->set('locale', 'en')
        ->call('updateLocale')
        ->assertHasNoErrors()
        ->assertRedirect('/settings/language');

    expect(session('locale'))->toBe('en');
});

test('an invalid locale is rejected', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Language::class)
        ->set('locale', 'fr')
        ->call('updateLocale')
        ->assertHasErrors(['locale']);

    expect(session('locale'))->toBeNull();
});
