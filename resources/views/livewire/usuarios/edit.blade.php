<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Usuário')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->usuario->name"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Usuários'), 'href' => route('usuarios.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('usuarios.show', $this->usuario) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Dados básicos do usuário')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="name" type="text" required autofocus />
                            <flux:error name="name" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('E-mail') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="email" type="email" required icon="envelope" />
                            <flux:error name="email" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Nova senha') }}</flux:label>
                            <flux:input wire:model="password" type="password" viewable icon="key" :placeholder="__('Deixe em branco para manter')" />
                            <flux:error name="password" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Confirmar nova senha') }}</flux:label>
                            <flux:input wire:model="password_confirmation" type="password" viewable icon="key" :placeholder="__('Deixe em branco para manter')" />
                            <flux:error name="password_confirmation" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Acesso --}}
            <section id="acesso" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="shield-check"
                    :title="__('Acesso')"
                    :description="__('Perfil de permissão e status do usuário')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Perfil') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="role">
                                <flux:select.option value="user">{{ __('Usuário') }}</flux:select.option>
                                <flux:select.option value="admin">{{ __('Administrador') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="role" />
                        </flux:field>

                        <flux:field class="flex items-end">
                            @if ($this->usuario->id === auth()->id())
                                <flux:switch wire:model="ativo" :label="__('Usuário ativo')" :disabled="true" />
                            @else
                                <flux:switch wire:model="ativo" :label="__('Usuário ativo')" />
                            @endif
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 80ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('usuarios.show', $this->usuario) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Usuário') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['acesso', 'shield-check', __('Acesso')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Para alterar a senha, informe a nova senha e a confirmação. Deixe em branco para manter a atual.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>