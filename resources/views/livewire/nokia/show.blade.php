@php
    $statusStyles = [
        'Planejamento' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Em andamento' => ['badge' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
        'Pausado' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Concluído' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Cancelado' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $statusStyle = $statusStyles[$this->projeto->status] ?? ['badge' => 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $fmtDate = fn ($value): string => $value?->format('d/m/Y') ?? '—';
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('nokia.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para projetos Nokia')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('nokia.index')" wire:navigate>{{ __('Projetos Nokia') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->projeto->codigo }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('nokia.edit', $this->projeto) }}" wire:navigate variant="primary" icon="pencil">
                {{ __('Editar') }}
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
    <section class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-violet-50/70 p-6 sm:p-8 dark:border-white/10 dark:from-white/[0.06] dark:via-white/[0.03] dark:to-violet-400/[0.05]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/15"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-sky-600 text-lg font-bold tracking-wide text-white shadow-lg shadow-violet-500/30 ring-4 ring-violet-500/10">
                    <flux:icon.folder class="size-7" />
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->projeto->codigo }}</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle['badge'] }}">
                            <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle['dot'] }}"></span>
                            {{ $this->projeto->status }}
                        </span>
                        @if ($this->projeto->ativo)
                            <flux:badge color="emerald" rounded>{{ __('Ativo') }}</flux:badge>
                        @else
                            <flux:badge color="gray" rounded>{{ __('Inativo') }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <flux:icon.folder class="size-4 text-violet-500 dark:text-violet-400" />
                            {{ $this->projeto->nome }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-3 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Início') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->projeto->data_inicio) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Fim') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->projeto->data_fim) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('OS vinculadas') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->ordens->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- OS vinculadas --}}
    <section id="ordens" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
        <header class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.clipboard-document-list class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Ordens de serviço vinculadas') }}</h3>
            </div>
            @if ($this->ordens->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    {{ $this->ordens->count() }} {{ __('ordem(ns)') }}
                </span>
            @endif
        </header>

        {{-- Vincular nova OS --}}
        <div class="mb-6 rounded-xl border border-dashed border-violet-200 bg-violet-50/40 p-4 dark:border-violet-400/20 dark:bg-violet-400/5">
            <p class="mb-3 flex items-center gap-2 text-sm font-medium text-zinc-900 dark:text-white">
                <flux:icon.link class="size-4 text-violet-500 dark:text-violet-400" />
                {{ __('Vincular ordem de serviço') }}
            </p>
            <flux:input
                wire:model.live="buscaOs"
                :placeholder="__('Buscar OS pelo código ou título...')"
                icon="magnifying-glass"
            />
            @if ($this->ordensDisponiveis->isNotEmpty())
                <div class="mt-3 flex flex-col divide-y divide-zinc-100 rounded-xl border border-zinc-200 bg-white dark:divide-white/5 dark:border-white/10">
                    @foreach ($this->ordensDisponiveis as $ordem)
                        <div class="flex items-center gap-3 px-4 py-2.5">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $ordem->codigo }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
                            </div>
                            <flux:button wire:click="vincular({{ $ordem->id }})" size="sm" variant="primary" icon="plus">
                                {{ __('Vincular') }}
                            </flux:button>
                        </div>
                    @endforeach
                </div>
            @elseif ($this->buscaOs !== '')
                <p class="mt-3 text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma ordem disponível para vincular.') }}</p>
            @endif
        </div>

        @if ($this->ordens->isEmpty())
            <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                {{ __('Nenhuma ordem de serviço vinculada a este projeto.') }}
            </p>
        @else
            <div class="flex flex-col">
                @foreach ($this->ordens as $ordem)
                    <div wire:key="projeto-os-{{ $ordem->id }}" class="group flex items-center gap-4 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                            <flux:icon.clipboard-document-list class="size-4.5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="truncate text-sm font-medium text-zinc-900 transition-colors hover:text-sky-600 dark:text-white dark:hover:text-sky-400">
                                {{ $ordem->codigo }}
                            </a>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <flux:button
                                wire:click="desvincular({{ $ordem->id }})"
                                wire:confirm="{{ __('Desvincular esta ordem?') }}"
                                size="sm"
                                variant="ghost"
                                icon="x-mark"
                                :aria-label="__('Desvincular')"
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir projeto') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->projeto->codigo }}</strong>? {{ __('As ordens de serviço vinculadas serão desvinculadas.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif