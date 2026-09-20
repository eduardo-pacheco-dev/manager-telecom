<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Hero / Page header --}}
    <div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-emerald-500/5 dark:from-sky-400/10 dark:via-transparent dark:to-emerald-400/10"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 size-56 rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-400/15"></div>

        <div class="relative flex flex-col gap-5 p-5 sm:p-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Ordens de Serviço') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <flux:heading size="xl" level="1" class="flex items-center gap-3">
                        {{ __('Ordens de Serviço') }}
                        <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-sky-500/10 px-2.5 py-0.5 text-sm font-semibold text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                            {{ $this->stats['total'] }}
                        </span>
                    </flux:heading>
                    <flux:subheading size="lg" class="mt-1">{{ __('Gerencie as ordens de serviço dos enlaces') }}</flux:subheading>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button
                        href="{{ route('ordens-servico.tipos') }}"
                        wire:navigate
                        variant="ghost"
                        icon="tag"
                        :title="__('Gerenciar tipos')"
                    >
                        {{ __('Tipos') }}
                    </flux:button>
                    <flux:button
                        wire:click="abrirImportacao"
                        variant="filled"
                        icon="arrow-up-tray"
                    >
                        {{ __('Importar') }}
                    </flux:button>
                    <flux:button href="{{ route('ordens-servico.create') }}" wire:navigate variant="primary" icon="plus">
                        {{ __('Nova Ordem de Serviço') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $total = (int) $this->stats['total'];
        $abertas = (int) $this->stats['abertas'];
        $concluidas = (int) $this->stats['concluidas'];
        $urgentes = (int) $this->stats['urgentes'];

        $pctAbertas = $total > 0 ? (int) round(($abertas / $total) * 100) : 0;
        $pctConcluidas = $total > 0 ? (int) round(($concluidas / $total) * 100) : 0;
        $pctUrgentes = $total > 0 ? (int) round(($urgentes / $total) * 100) : 0;
    @endphp

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-zinc-200/40 blur-2xl dark:bg-white/5"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de ordens') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $total }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.clipboard-document-list class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-zinc-400 to-zinc-600 dark:from-zinc-500 dark:to-zinc-300" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Inventário completo') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-sky-200/40 blur-2xl dark:bg-sky-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Em aberto') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $abertas }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 transition-transform group-hover:scale-105 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.clock class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-sky-500/10 dark:bg-sky-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-sky-400 transition-all duration-700" style="width: {{ $pctAbertas }}%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $pctAbertas }}% {{ __('do total') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Concluídas') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $concluidas }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.check-circle class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-emerald-500/10 dark:bg-emerald-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-700" style="width: {{ $pctConcluidas }}%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $pctConcluidas }}% {{ __('do total') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-rose-200/40 blur-2xl dark:bg-rose-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Urgentes') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-rose-600 dark:text-rose-400">{{ $urgentes }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 transition-transform group-hover:scale-105 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.exclamation-triangle class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-rose-500/10 dark:bg-rose-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-rose-400 transition-all duration-700" style="width: {{ $pctUrgentes }}%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $pctUrgentes }}% {{ __('do total') }}</p>
            </div>
        </div>
    </section>

    {{-- Importações: notifica via toast --}}
    <div wire:poll.5s="verificarImportacoes" class="hidden" aria-hidden="true"></div>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 260ms">
        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto_auto_auto_auto] sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por código, título, solicitante ou link...')"
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-36">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                @foreach ($this->statuses as $status)
                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroTipo" class="w-full sm:w-36">
                <flux:select.option value="">{{ __('Todos os tipos') }}</flux:select.option>
                @foreach ($this->tipos as $tipo)
                    <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroPrioridade" class="w-full sm:w-32">
                <flux:select.option value="">{{ __('Prioridade') }}</flux:select.option>
                @foreach (\App\Models\OrdemServico::PRIORIDADES as $prioridade)
                    <flux:select.option :value="$prioridade">{{ $prioridade }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="perPage" class="w-full sm:w-32" :label="__('Itens por página')">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
                <flux:select.option value="100">100</flux:select.option>
            </flux:select>
        </div>

        {{-- Quick filter chips --}}
        <div class="mt-3 flex flex-wrap items-center gap-1.5 border-t border-zinc-100 pt-3 dark:border-white/5">
            <span class="mr-1 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Status') }}:</span>
            @foreach (['', ...\App\Models\OrdemServico::STATUS] as $chipStatus)
                <button
                    type="button"
                    wire:click="$set('filtroStatus', '{{ $chipStatus }}')"
                    class="inline-flex cursor-pointer items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                        {{ $filtroStatus === $chipStatus
                            ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/30'
                            : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:hover:bg-white/20' }}"
                >
                    {{ $chipStatus === '' ? __('Todos') : $chipStatus }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 300ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($ordensServico->total() > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $ordensServico->firstItem() }}-{{ $ordensServico->lastItem() }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $ordensServico->total() }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            <div class="flex items-center gap-3">
                <div wire:loading.delay wire:target="search,filtroStatus,filtroTipo,filtroPrioridade,sortBy,perPage" class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                    <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('Carregando...') }}
                </div>

                @if ($search !== '' || $filtroStatus !== '' || $filtroTipo !== '' || $filtroPrioridade !== '')
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="inline-flex cursor-pointer items-center gap-1.5 text-sm font-medium text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                    >
                        <flux:icon.arrow-path class="size-3.5" />
                        {{ __('Limpar filtros') }}
                    </button>
                @endif
            </div>
        </div>

        <div wire:loading.class="opacity-40" wire:target="search,filtroStatus,filtroTipo,filtroPrioridade,sortBy,perPage" class="transition-opacity duration-200">
            {{-- Column Headers (sortable) --}}
            @if ($ordensServico->total() > 0)
                <div class="hidden items-center gap-4 border-b border-zinc-100 bg-zinc-50/60 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:bg-white/[0.02] dark:text-zinc-500 md:flex">
                    <button type="button" wire:click="sortBy('codigo')" class="group/col flex min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Ordem') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'codigo'])
                    </button>

                    <button type="button" wire:click="sortBy('titulo')" class="group/col hidden min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 sm:flex dark:hover:text-zinc-200">
                        {{ __('Título') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'titulo'])
                    </button>

                    <button type="button" wire:click="sortBy('tipo')" class="group/col hidden w-28 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Tipo') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'tipo'])
                    </button>

                    <button type="button" wire:click="sortBy('prioridade')" class="group/col hidden w-24 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Prioridade') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'prioridade'])
                    </button>

                    <button type="button" wire:click="sortBy('data_abertura')" class="group/col hidden w-24 shrink-0 cursor-pointer items-center justify-end gap-1 text-right transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Abertura') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'data_abertura'])
                    </button>

                    <button type="button" wire:click="sortBy('status')" class="group/col flex w-28 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Status') }}
                        @include('livewire.ordens-servico.partials.sort-indicator', ['field' => 'status'])
                    </button>

                    <div class="w-24 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>
            @endif

            @forelse ($ordensServico as $ordem)
                @php
                    $avatarTints = [
                        'bg-sky-500/15 text-sky-700 dark:text-sky-300',
                        'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
                        'bg-violet-500/15 text-violet-700 dark:text-violet-300',
                        'bg-amber-500/15 text-amber-700 dark:text-amber-300',
                        'bg-rose-500/15 text-rose-700 dark:text-rose-300',
                        'bg-teal-500/15 text-teal-700 dark:text-teal-300',
                    ];
                    $tint = $avatarTints[$ordem->id % count($avatarTints)];

                    $statusStyles = [
                        'Aberta' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                        'Em andamento' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'Aguardando' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
                        'Concluída' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                        'Cancelada' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                    ];
                    $statusStyle = $statusStyles[$ordem->status] ?? 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300';

                    $statusDots = [
                        'Aberta' => 'bg-sky-500 dark:bg-sky-400',
                        'Em andamento' => 'bg-amber-500 dark:bg-amber-400',
                        'Aguardando' => 'bg-zinc-400 dark:bg-zinc-500',
                        'Concluída' => 'bg-emerald-500 dark:bg-emerald-400',
                        'Cancelada' => 'bg-rose-500 dark:bg-rose-400',
                    ];
                    $statusDot = $statusDots[$ordem->status] ?? 'bg-zinc-400 dark:bg-zinc-500';

                    $prioridadeStyles = [
                        'Baixa' => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300',
                        'Média' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                        'Alta' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'Urgente' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                    ];
                    $prioridadeStyle = $prioridadeStyles[$ordem->prioridade] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';

                    $prioridadeDots = [
                        'Baixa' => 'bg-zinc-400 dark:bg-zinc-500',
                        'Média' => 'bg-sky-500 dark:bg-sky-400',
                        'Alta' => 'bg-amber-500 dark:bg-amber-400',
                        'Urgente' => 'bg-rose-500 dark:bg-rose-400',
                    ];
                    $prioridadeDot = $prioridadeDots[$ordem->prioridade] ?? 'bg-zinc-400 dark:bg-zinc-500';
                @endphp

                {{-- Desktop row --}}
                <div class="group hidden items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-all duration-200 last:border-b-0 hover:bg-zinc-50/80 md:flex dark:border-white/5 dark:hover:bg-white/[0.02]">
                    {{-- Codigo + link --}}
                    <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                            {{ \Illuminate\Support\Str::limit($ordem->codigo, 5, '') }}
                            @if ($ordem->prioridade === 'Urgente')
                                <span class="absolute -right-1 -top-1 flex size-3">
                                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                    <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
                                </span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $ordem->codigo }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->radioLink?->codigo ?: __('Sem link associado') }}</p>
                        </div>
                    </a>

                    {{-- Título --}}
                    <div class="hidden min-w-0 flex-1 sm:block">
                        <p class="truncate text-sm text-zinc-700 dark:text-zinc-300">{{ $ordem->titulo }}</p>
                        @if ($ordem->solicitante)
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->solicitante }}</p>
                        @endif
                    </div>

                    {{-- Tipo --}}
                    <div class="hidden w-28 shrink-0 lg:block">
                        @if ($ordem->tipo)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $ordem->tipo }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Prioridade --}}
                    <div class="hidden w-24 shrink-0 md:block">
                        @if ($ordem->prioridade)
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $prioridadeStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full {{ $prioridadeDot }}"></span>
                                {{ $ordem->prioridade }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Abertura --}}
                    <div class="hidden w-24 text-right lg:block">
                        @if ($ordem->data_abertura)
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->data_abertura->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-28 shrink-0">
                        @if ($ordem->status)
                            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full {{ $statusDot }}"></span>
                                <span class="truncate">{{ $ordem->status }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-400 dark:bg-white/5 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex w-24 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <flux:button
                            href="{{ route('ordens-servico.show', $ordem) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $ordem->codigo"
                        />
                        <flux:button
                            href="{{ route('ordens-servico.edit', $ordem) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $ordem->codigo"
                        />
                        <flux:button
                            wire:click="destroy({{ $ordem->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $ordem->codigo"
                        />
                    </div>
                </div>

                {{-- Mobile card --}}
                <div class="group border-b border-zinc-100 p-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:hidden dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="flex min-w-0 items-center gap-3">
                            <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                                {{ \Illuminate\Support\Str::limit($ordem->codigo, 5, '') }}
                                @if ($ordem->prioridade === 'Urgente')
                                    <span class="absolute -right-1 -top-1 flex size-3">
                                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $ordem->codigo }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
                            </div>
                        </a>

                        @if ($ordem->status)
                            <span class="inline-flex max-w-full shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full {{ $statusDot }}"></span>
                                <span class="truncate">{{ $ordem->status }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                        @if ($ordem->radioLink)
                            <span class="inline-flex items-center gap-1.5">
                                <flux:icon.radio class="size-3.5 shrink-0 text-sky-500 dark:text-sky-400" />
                                <span>{{ $ordem->radioLink->codigo }}</span>
                            </span>
                        @endif
                        @if ($ordem->tipo)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $ordem->tipo }}
                            </span>
                        @endif
                        @if ($ordem->prioridade)
                            <span class="inline-flex items-center gap-1.5">
                                <span class="size-1.5 rounded-full {{ $prioridadeDot }}"></span>
                                <span class="font-medium {{ str_contains($prioridadeStyle, 'text-rose') ? 'text-rose-600 dark:text-rose-400' : (str_contains($prioridadeStyle, 'text-amber') ? 'text-amber-600 dark:text-amber-400' : (str_contains($prioridadeStyle, 'text-sky') ? 'text-sky-600 dark:text-sky-400' : 'text-zinc-600 dark:text-zinc-300')) }}">
                                    {{ $ordem->prioridade }}
                                </span>
                            </span>
                        @endif
                        @if ($ordem->data_abertura)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.calendar-days class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ $ordem->data_abertura->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-1 border-t border-zinc-100 pt-3 dark:border-white/5">
                        <flux:button
                            href="{{ route('ordens-servico.show', $ordem) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $ordem->codigo"
                        />
                        <flux:button
                            href="{{ route('ordens-servico.edit', $ordem) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $ordem->codigo"
                        />
                        <flux:button
                            wire:click="destroy({{ $ordem->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $ordem->codigo"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.clipboard-document-list class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhuma ordem de serviço encontrada') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Tente ajustar sua busca ou os filtros para encontrar o que procura.') }}
                    </p>
                    @if ($search !== '' || $filtroStatus !== '' || $filtroTipo !== '' || $filtroPrioridade !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('ordens-servico.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-5">
                            {{ __('Cadastrar primeira ordem') }}
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($ordensServico->hasPages())
            <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Mostrando') }} {{ $ordensServico->firstItem() }} {{ __('a') }} {{ $ordensServico->lastItem() }} {{ __('de') }} {{ $ordensServico->total() }} {{ __('resultados') }}
                </div>
                <div class="flex items-center gap-1">
                    @if ($ordensServico->onFirstPage())
                        <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                            <flux:icon.chevron-left variant="micro" />
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage"
                            :aria-label="__('Página anterior')"
                            class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            <flux:icon.chevron-left variant="micro" />
                        </button>
                    @endif

                    @foreach ($ordensServico->getUrlRange(max(1, $ordensServico->currentPage() - 1), min($ordensServico->lastPage(), $ordensServico->currentPage() + 1)) as $page => $url)
                        @if ($page == $ordensServico->currentPage())
                            <span class="flex size-8 items-center justify-center rounded-lg bg-zinc-900 text-xs font-medium text-white dark:bg-white dark:text-zinc-900">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="gotoPage({{ $page }})"
                                class="flex size-8 items-center justify-center rounded-lg text-xs font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach

                    @if ($ordensServico->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage"
                            :aria-label="__('Próxima página')"
                            class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            <flux:icon.chevron-right variant="micro" />
                        </button>
                    @else
                        <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                            <flux:icon.chevron-right variant="micro" />
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal de importação --}}
    <flux:modal wire:model="showImportModal" class="max-w-2xl">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">{{ __('Importar ordens de serviço') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Envie um arquivo Excel (.xlsx) ou CSV com as ordens de serviço. A importação roda em segundo plano via fila de jobs, ideal para arquivos com milhares de linhas.') }}
                </flux:text>
            </div>

            {{-- Dropzone --}}
            <div
                x-data="{ dragging: false }"
                @dragover.prevent="dragging = true"
                @dragenter.prevent="dragging = true"
                @dragleave="dragging = false"
                @drop.prevent="
                    dragging = false;
                    const files = $event.dataTransfer.files;
                    if (files.length) $wire.upload('import_arquivo', files[0]);
                "
                class="group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed transition-all duration-200"
                :class="dragging
                    ? 'border-sky-400 bg-sky-50/70 dark:border-sky-500 dark:bg-sky-400/10'
                    : 'border-zinc-300 bg-zinc-50/50 hover:border-sky-300 hover:bg-sky-50/40 dark:border-white/15 dark:bg-white/[0.03] dark:hover:border-sky-500/50 dark:hover:bg-sky-400/5'"
                @click="$refs.fileInput.click()"
            >
                <input
                    type="file"
                    wire:model="import_arquivo"
                    accept=".xlsx,.csv"
                    class="sr-only"
                    x-ref="fileInput"
                />

                <div class="flex flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                    <div
                        class="flex size-14 items-center justify-center rounded-2xl transition-colors duration-200"
                        :class="dragging
                            ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/30'
                            : 'bg-sky-500/10 text-sky-600 group-hover:bg-sky-500/15 dark:bg-sky-400/10 dark:text-sky-400'"
                    >
                        <template x-if="!@js($import_arquivo !== null)">
                            <flux:icon.arrow-up-tray class="size-7" />
                        </template>
                        <template x-if="@js($import_arquivo !== null)">
                            <flux:icon.document-check class="size-7" />
                        </template>
                    </div>

                    <div class="space-y-1">
                        <p
                            class="text-sm font-semibold text-zinc-900 dark:text-white"
                            x-show="@js($import_arquivo === null)"
                        >
                            {{ __('Arraste o arquivo aqui') }}
                        </p>
                        <p
                            class="truncate text-sm font-semibold text-zinc-900 dark:text-white"
                            x-show="@js($import_arquivo !== null)"
                        >
                            @if ($import_arquivo !== null)
                                {{ $import_arquivo->getClientOriginalName() }}
                            @endif
                        </p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            <span x-show="@js($import_arquivo === null)">
                                {{ __('ou clique para selecionar') }} · <strong>.xlsx</strong> / <strong>.csv</strong> · {{ __('até 200 MB') }}
                            </span>
                            <span x-show="@js($import_arquivo !== null)">
                                {{ __('Arquivo selecionado. Clique para trocar.') }}
                            </span>
                        </p>
                    </div>

                    <flux:button
                        as="button"
                        type="button"
                        variant="subtle"
                        size="sm"
                        class="pointer-events-none"
                    >
                        <flux:icon.folder class="size-4" />
                        {{ __('Selecionar arquivo') }}
                    </flux:button>
                </div>
            </div>

            <flux:error name="import_arquivo" />

            {{-- Arquivo selecionado --}}
            @if ($import_arquivo !== null)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-4 py-3 dark:border-emerald-400/20 dark:bg-emerald-400/10">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                            <flux:icon.document-check class="size-4.5" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $import_arquivo->getClientOriginalName() }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ number_format($import_arquivo->getSize() / 1048576, 1, ',', '.') }} MB
                            </p>
                        </div>
                    </div>
                    <flux:button
                        wire:click="limparArquivoImportacao"
                        variant="ghost"
                        icon="x-mark"
                        size="sm"
                        :aria-label="__('Remover arquivo')"
                    />
                </div>
            @endif

            {{-- Colunas esperadas --}}
            <div class="rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ __('Colunas reconhecidas') }}:</p>
                <div class="mt-1.5 flex flex-wrap gap-1.5">
                    @foreach (['Cód_AFL', 'Status_Geral', 'Site_ID A', 'Site_ID B', 'END_ID A', 'END_ID B', 'Projeto', 'Descrição', 'Supervisor', 'Coordenador', 'OC (TIM)', 'Chave_MW', 'SMP_Nokia', 'OBS GERAL', 'Data_Cadastro_Ativ'] as $coluna)
                        <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-[11px] text-zinc-600 dark:bg-white/10 dark:text-zinc-300">{{ $coluna }}</span>
                    @endforeach
                </div>
                <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                    {{ __('Obrigatória') }}: <strong>Cód_AFL</strong>.
                    {{ __('As estações A/B são resolvidas pelo Site ID; linhas sem código ou sem estação correspondente são ignoradas.') }}
                </p>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    icon="arrow-up-tray"
                    wire:click="iniciarImportacao"
                    wire:loading.attr="disabled"
                    wire:target="iniciarImportacao"
                >
                    {{ __('Iniciar importação') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>