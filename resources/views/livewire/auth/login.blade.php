<x-layouts::auth.card :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon.shield-check class="size-6" />
            </div>
            <h2 class="mt-4 text-xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                {{ __('Bem-vindo de volta') }}
            </h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Entre com suas credenciais para acessar o sistema.') }}
            </p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input
                    name="email"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@exemplo.com"
                    icon="envelope"
                />
                <flux:error name="email" />
            </flux:field>

            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <flux:label>{{ __('Senha') }}</flux:label>

                    @if (Route::has('password.request'))
                        <flux:link class="text-sm" :href="route('password.request')" wire:navigate>
                            {{ __('Esqueceu a senha?') }}
                        </flux:link>
                    @endif
                </div>

                <flux:input
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Sua senha')"
                    viewable
                    icon="key"
                />
                <flux:error name="password" />
            </div>

            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Manter conectado')" :checked="old('remember')" />

                <flux:button variant="primary" type="submit" icon="arrow-right-end-on-rectangle" data-test="login-button">
                    {{ __('Entrar') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                <span class="h-px flex-1 bg-zinc-200 dark:bg-white/10"></span>
                <span>{{ __('Ainda não tem conta?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Cadastre-se') }}</flux:link>
                <span class="h-px flex-1 bg-zinc-200 dark:bg-white/10"></span>
            </div>
        @endif
    </div>
</x-layouts::auth.card>