<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group expandable icon="home" :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group expandable icon="folder" :heading="__('Gestão')" class="grid">
                    <flux:sidebar.item icon="users" :href="route('colaboradores.index')" :current="request()->routeIs('colaboradores.*')" wire:navigate>
                        {{ __('Colaboradores') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('clientes.index')" :current="request()->routeIs('clientes.*')" wire:navigate>
                        {{ __('Clientes') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cube" :href="route('produtos.index')" :current="request()->routeIs('produtos.*')" wire:navigate>
                        {{ __('Produtos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wrench-screwdriver" :href="route('servicos.index')" :current="request()->routeIs('servicos.*')" wire:navigate>
                        {{ __('Serviços') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="signal" :href="route('estacoes.index')" :current="request()->routeIs('estacoes.*')" wire:navigate>
                        {{ __('Estações') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="radio" :href="route('radio-links.index')" :current="request()->routeIs('radio-links.*')" wire:navigate>
                        {{ __('Radio Links') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('ordens-servico.index')" :current="request()->routeIs('ordens-servico.*')" wire:navigate>
                        {{ __('Ordens de Serviço') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('usuarios.index')" :current="request()->routeIs('usuarios.*')" wire:navigate>
                        {{ __('Usuários') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('storage.index')" :current="request()->routeIs('storage.*')" wire:navigate>
                        {{ __('Armazenamento') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group expandable icon="folder" :heading="__('Projetos')" class="grid">
                    <flux:sidebar.item icon="folder" :href="route('nokia.index')" :current="request()->routeIs('nokia.*')" wire:navigate>
                        {{ __('Nokia') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
