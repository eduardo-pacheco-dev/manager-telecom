@php
    $fmtDate = fn ($value): string => $value?->format('d/m/Y') ?? '—';

    $roleStyle = $this->usuario->role === 'admin'
        ? ['badge' => 'bg-violet-500/10 text-violet-700 ring-1 ring-inset ring-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/20', 'dot' => 'bg-violet-500 dark:bg-violet-400']
        : ['badge' => 'bg-zinc-100 text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $tints = [
        'bg-sky-500/15 text-sky-700 ring-sky-500/20 dark:text-sky-300',
        'bg-emerald-500/15 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300',
        'bg-violet-500/15 text-violet-700 ring-violet-500/20 dark:text-violet-300',
        'bg-amber-500/15 text-amber-700 ring-amber-500/20 dark:text-amber-300',
        'bg-rose-500/15 text-rose-700 ring-rose-500/20 dark:text-rose-300',
        'bg-teal-500/15 text-teal-700 ring-teal-500/20 dark:text-teal-300',
    ];
    $tint = $tints[$this->usuario->id % count($tints)];

    $navSections = [
        ['id' => 'identificacao', 'label' => __('Identificação'), 'icon' => 'identification'],
        ['id' => 'acesso', 'label' => __('Acesso'), 'icon' => 'shield-check'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6 scroll-smooth">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('usuarios.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para usuários')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('usuarios.index')" wire:navigate>{{ __('Usuários') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->usuario->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('usuarios.edit', $this->usuario) }}" wire:navigate variant="primary" icon="pencil">
                {{ __('Editar') }}
            </flux:button>
            @if ($this->usuario->id !== auth()->id())
                <flux:button
                    wire:click="confirmDelete"
                    variant="danger"
                    icon="trash"
                    :aria-label="__('Excluir')"
                    :title="__('Excluir')"
                />
            @endif
        </div>
    </div>

    {{-- Hero --}}
    <section class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-sky-50/70 p-6 sm:p-8 dark:border-white/10 dark:from-white/[0.06] dark:via-white/[0.03] dark:to-sky-400/[0.05]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-400/15"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-violet-600 text-lg font-bold tracking-wide text-white shadow-lg shadow-sky-500/30 ring-4 ring-sky-500/10">
                    {{ $this->usuario->initials() }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->usuario->name }}</h2>
                        <flux:badge :color="$this->usuario->role === 'admin' ? 'violet' : 'gray'" rounded>
                            {{ $this->usuario->role === 'admin' ? __('Administrador') : __('Usuário') }}
                        </flux:badge>
                        @if ($this->usuario->ativo)
                            <flux:badge color="emerald" rounded>{{ __('Ativo') }}</flux:badge>
                        @else
                            <flux:badge color="gray" rounded>{{ __('Inativo') }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <flux:icon.envelope class="size-4 text-sky-500 dark:text-sky-400" />
                            {{ $this->usuario->email }}
                        </span>
                        @if ($this->usuario->id === auth()->id())
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span>{{ __('Este é você') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-3 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Perfil') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->usuario->role === 'admin' ? __('Administrador') : __('Usuário') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Verificado em') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->usuario->email_verified_at) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Criado em') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->usuario->created_at) }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick nav (scrollspy) --}}
    <nav
        x-data="{ active: '{{ $navSections[0]['id'] }}' }"
        @scroll.window.passive="
            const offset = 160;
            let current = '{{ $navSections[0]['id'] }}';
            document.querySelectorAll('[data-section]').forEach((el) => {
                if (el.getBoundingClientRect().top <= offset) current = el.id;
            });
            active = current;
        "
        class="animate-fade-in-up sticky top-4 z-20 flex gap-1.5 overflow-x-auto rounded-2xl border border-zinc-200 bg-white/90 p-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90"
        style="animation-delay: 80ms"
        aria-label="{{ __('Navegação rápida') }}"
    >
        @foreach ($navSections as $section)
            <a
                href="#{{ $section['id'] }}"
                @click="active = '{{ $section['id'] }}'"
                :class="active === '{{ $section['id'] }}'
                    ? 'bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-900'
                    : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white'"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
            >
                <flux:icon :icon="$section['icon']" variant="micro" class="size-4" />
                {{ $section['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Identificação --}}
    <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon.identification class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Identificação') }}</h3>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['label' => __('Nome'), 'value' => $this->usuario->name],
                ['label' => __('E-mail'), 'value' => $this->usuario->email],
                ['label' => __('Verificado em'), 'value' => $fmtDate($this->usuario->email_verified_at)],
                ['label' => __('Criado em'), 'value' => $fmtDate($this->usuario->created_at)],
            ] as $item)
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                    <p class="mt-1.5 break-words text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Acesso --}}
    <section id="acesso" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 160ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                <flux:icon.shield-check class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Acesso') }}</h3>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Perfil') }}</p>
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $roleStyle['badge'] }}">
                        <span class="size-1.5 shrink-0 rounded-full {{ $roleStyle['dot'] }}"></span>
                        {{ $this->usuario->role === 'admin' ? __('Administrador') : __('Usuário') }}
                    </span>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Status') }}</p>
                <div class="mt-2">
                    @if ($this->usuario->ativo)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">
                            <span class="size-1.5 shrink-0 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                            {{ __('Ativo') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-500/10 px-2.5 py-1 text-xs font-medium text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20">
                            <span class="size-1.5 shrink-0 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
                            {{ __('Inativo') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir usuário') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->usuario->name }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif