@php
    $statusStyles = [
        'Pendente' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Em andamento' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Concluído' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Cancelado' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $statusStyle = $statusStyles[$this->relatorio->status] ?? ['badge' => 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $fmtDate = fn ($value): string => $value?->format('d/m/Y') ?? '—';
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('tim.show', $this->projeto) }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para o projeto')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('tim.index')" wire:navigate>{{ __('Projetos TIM') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('tim.show', $this->projeto)" wire:navigate>{{ $this->projeto->codigo }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->relatorio->ordemServico?->codigo ?: 'Relatório #'.$this->relatorio->id }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
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
                    <flux:icon.document-text class="size-7" />
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->relatorio->ordemServico?->codigo ?: 'Relatório #'.$this->relatorio->id }}</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle['badge'] }}">
                            <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle['dot'] }}"></span>
                            {{ $this->relatorio->status }}
                        </span>
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <flux:icon.folder class="size-4 text-violet-500 dark:text-violet-400" />
                            {{ $this->projeto->codigo }} · {{ $this->projeto->nome }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-x-8 gap-y-6 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Início') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->relatorio->data_inicio) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Planejada') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->relatorio->data_planejada) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Real') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->relatorio->data_real) }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Vínculos --}}
    <section id="vinculos" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon.link class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Vínculos') }}</h3>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Ordem de serviço') }}</p>
                <div class="mt-2">
                    @if ($this->relatorio->ordemServico)
                        <a href="{{ route('ordens-servico.show', $this->relatorio->ordemServico) }}" wire:navigate class="text-sm font-semibold text-sky-600 hover:underline dark:text-sky-400">
                            {{ $this->relatorio->ordemServico->codigo }}
                        </a>
                        <p class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->relatorio->ordemServico->titulo }}</p>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">—</p>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Site ID') }}</p>
                <div class="mt-2">
                    @if ($this->relatorio->estacao)
                        <a href="{{ route('estacoes.show', $this->relatorio->estacao) }}" wire:navigate class="text-sm font-semibold text-sky-600 hover:underline dark:text-sky-400">
                            {{ $this->relatorio->estacao->site_id }}
                        </a>
                        <p class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->relatorio->estacao->municipio ?: '—' }}</p>
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">—</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Observação --}}
    @if ($this->relatorio->observacao)
        <section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 160ms">
            <header class="mb-4 flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-zinc-700/10 text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                    <flux:icon.document-text class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Observação') }}</h3>
            </header>
            <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->relatorio->observacao }}</p>
        </section>
    @endif
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir relatório') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir este relatório? Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif