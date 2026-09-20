<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'user:admin
        {--name= : Nome do administrador}
        {--email= : E-mail do administrador}
        {--password= : Senha do administrador}';

    protected $description = 'Cria um usuário administrador no sistema';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Nome do administrador', 'Administrador');
        $email = $this->option('email') ?? $this->ask('E-mail do administrador');

        $temPasswordViaOption = $this->option('password') !== null;

        if ($temPasswordViaOption) {
            $password = $this->option('password');
        } else {
            $password = $this->secret('Senha do administrador');
            $confirmacao = $this->secret('Confirme a senha do administrador');

            if ($password !== $confirmacao) {
                $this->components->error('As senhas não conferem.');

                return self::FAILURE;
            }
        }

        if (! $this->validate($name, $email, $password)) {
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => 'admin',
                'ativo' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->components->info("Usuário administrador '{$user->email}' criado com sucesso.");

        return self::SUCCESS;
    }

    private function validate(string $name, string $email, string $password): bool
    {
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $erro) {
                $this->components->error($erro);
            }

            return false;
        }

        return true;
    }
}
