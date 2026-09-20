<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Editar Usuário')]
class Edit extends Component
{
    public ?User $usuario = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'user';

    public bool $ativo = true;

    public function mount(User $usuario): void
    {
        $this->usuario = $usuario;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->role = $usuario->role;
        $this->ativo = $usuario->ativo;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->usuario->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(User::ROLES)],
            'ativo' => ['boolean'],
        ]);

        $this->usuario->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'ativo' => $validated['ativo'],
        ]);

        if (filled($validated['password'])) {
            $this->usuario->update(['password' => $validated['password']]);
        }

        Flux::toast(variant: 'success', text: __('Usuário atualizado com sucesso.'));

        $this->redirect(route('usuarios.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.usuarios.edit');
    }
}
