@php
    $categoriaStyles = [
        'Equipamento' => ['badge' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
        'Infraestrutura' => ['badge' => 'bg-violet-500/10 text-violet-700 ring-1 ring-inset ring-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/20', 'dot' => 'bg-violet-500 dark:bg-violet-400'],
        'Acessório' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Cabeamento' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
    ];
    $categoriaStyle = $categoriaStyles[$this->produto->categoria] ?? ['badge' => 'bg-zinc-100 text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $avatarTints = [
        'bg-sky-500/15 text-sky-700 ring-sky-500/20 dark:text-sky-300',
        'bg-emerald-500/15 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300',
        'bg-violet-500/15 text-violet-700 ring-violet-500/20 dark:text-violet-300',
        'bg-amber-500/15 text-amber-700 ring-amber-500/20 dark:text-amber-300',
        'bg-rose-500/15 text-rose-700 ring-rose-500/20 dark:text-rose-300',
        'bg-teal-500/15 text-teal-700 ring-teal-500/20 dark:text-teal-300',
    ];
    $tint = $avatarTints[$this->produto->id % count($avatarTints)];

    $navSections = [
        ['id' => 'identificacao', 'label' => __('Identificação'), 'icon' => 'identification'],
        ['id' => 'descricao', 'label' => __('Descrição'), 'icon' => 'document-text'],
        ['id' => 'observacoes', 'label' => __('Observações'), 'icon' => 'chat-bubble-left-right'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6 scroll-smooth">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('produtos.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para produtos')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('produtos.index')" wire:navigate>{{ __('Produtos') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->produto->nome }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('produtos.edit', $this->produto) }}" wire:navigate variant="primary" icon="pencil">
                {{ __('Editar') }}
            </flux:button>
            <flux:button
                wire:click="toggleAtivo"
                variant="ghost"
                :icon="$this->produto->ativo ? 'x-mark' : 'check'"
                :title="$this->produto->ativo ? __('Desativar') : __('Ativar')"
            >
                {{ $this->produto->ativo ? __('Desativar') : __('Ativar') }}
            </flux:button>
            <flux:button
                wire:click="confirmDelete"
                variant="danger"
                icon="trash"
                :aria-label="__('Excluir')"
                :title="__('Excluir')"
            />
        </div>
    </div>

    {{-- Hero --}}
    <section class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-sky-50/70 p-4 sm:p-6 lg:p-8 dark:border-white/10 dark:from-white/[0.06] dark:via-white/[0.03] dark:to-sky-400/[0.05]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-400/15"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-xl text-base font-bold shadow-lg ring-4 ring-black/5 sm:size-16 sm:rounded-2xl sm:text-lg {{ $tint }}">
                    {{ \Illuminate\Support\Str::initials($this->produto->nome, true) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="truncate text-xl font-semibold tracking-tight text-zinc-900 sm:text-2xl dark:text-white">{{ $this->produto->nome }}</h2>
                        @if ($this->produto->categoria)
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $categoriaStyle['badge'] }}">
                                <span class="size-1.5 shrink-0 rounded-full {{ $categoriaStyle['dot'] }}"></span>
                                {{ $this->produto->categoria }}
                            </span>
                        @endif
                        @if ($this->produto->ativo)
                            <flux:badge color="emerald" rounded>{{ __('Ativo') }}</flux:badge>
                        @else
                            <flux:badge color="red" rounded>{{ __('Inativo') }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        @if ($this->produto->codigo)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.hashtag class="size-4 text-sky-500 dark:text-sky-400" />
                                {{ $this->produto->codigo }}
                            </span>
                        @endif
                        @if ($this->produto->preco !== null)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.banknotes class="size-4 text-emerald-500 dark:text-emerald-400" />
                                R$ {{ number_format($this->produto->preco, 2, ',', '.') }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl bg-white/60 p-3.5 backdrop-blur-sm sm:p-4 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                            <flux:icon.hashtag class="size-4" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Código') }}</p>
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->produto->codigo ?: '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white/60 p-3.5 backdrop-blur-sm sm:p-4 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                            <flux:icon.banknotes class="size-4" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Preço') }}</p>
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->produto->preco !== null ? 'R$ '.number_format($this->produto->preco, 2, ',', '.') : '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white/60 p-3.5 backdrop-blur-sm sm:p-4 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                            <flux:icon.tag class="size-4" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Categoria') }}</p>
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->produto->categoria ?: '—' }}</p>
                        </div>
                    </div>
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
    <x-ui.detail-card id="identificacao" data-section icon="identification" :title="__('Identificação')" delay="120ms">
        <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Código / SKU') }}</dt>
                <dd class="mt-1.5 flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <flux:icon.hashtag class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
                    {{ $this->produto->codigo ?: '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Categoria') }}</dt>
                <dd class="mt-1.5">
                    @if ($this->produto->categoria)
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $categoriaStyle['badge'] }}">
                            <span class="size-1.5 shrink-0 rounded-full {{ $categoriaStyle['dot'] }}"></span>
                            {{ $this->produto->categoria }}
                        </span>
                    @else
                        <span class="text-sm text-zinc-400 dark:text-zinc-500">—</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Preço') }}</dt>
                <dd class="mt-1.5 flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <flux:icon.banknotes class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
                    {{ $this->produto->preco !== null ? 'R$ '.number_format($this->produto->preco, 2, ',', '.') : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Status') }}</dt>
                <dd class="mt-1.5">
                    @if ($this->produto->ativo)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">
                            <span class="size-1.5 rounded-full bg-current"></span>
                            {{ __('Ativo') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20">
                            <span class="size-1.5 rounded-full bg-current"></span>
                            {{ __('Inativo') }}
                        </span>
                    @endif
                </dd>
            </div>
        </dl>
    </x-ui.detail-card>

    {{-- Descrição --}}
    <x-ui.detail-card id="descricao" data-section icon="document-text" :title="__('Descrição')" delay="160ms">
        @if ($this->produto->descricao)
            <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->produto->descricao }}</p>
        @else
            <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma descrição registrada.') }}</p>
        @endif
    </x-ui.detail-card>

    {{-- Observações --}}
    <x-ui.detail-card id="observacoes" data-section icon="chat-bubble-left-right" :title="__('Observações')" delay="200ms">
        @if ($this->produto->observacoes)
            <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->produto->observacoes }}</p>
        @else
            <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma observação registrada.') }}</p>
        @endif
    </x-ui.detail-card>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir produto') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->produto->nome }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif