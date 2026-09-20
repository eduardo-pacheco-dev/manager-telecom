<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Novo Usuário')]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'user';

    public bool $ativo = true;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(User::ROLES)],
            'ativo' => ['boolean'],
        ]);

        User::create($validated);

        Flux::toast(variant: 'success', text: __('Usuário criado com sucesso.'));

        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.usuarios.create');
    }
}
