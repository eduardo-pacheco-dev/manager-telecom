<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <div class="animate-fade-in-up flex flex-col gap-5">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Radio Links') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="xl" level="1" class="flex items-center gap-3">
                    {{ __('Radio Links') }}
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-zinc-100 px-2 py-0.5 text-sm font-semibold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                        {{ $this->stats['total'] }}
                    </span>
                </flux:heading>
                <flux:subheading size="lg" class="mt-1">{{ __('Gerencie os enlaces de rádio entre estações') }}</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                <flux:button
                    wire:click="abrirImportacao"
                    variant="filled"
                    icon="arrow-up-tray"
                >
                    {{ __('Importar') }}
                </flux:button>
                <flux:button href="{{ route('radio-links.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Novo Radio Link') }}
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Importações recentes --}}
    @if ($this->importacoes->isNotEmpty())
        <section class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 20ms" wire:poll.5s>
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
                <p class="flex items-center gap-2 text-sm font-medium text-zinc-900 dark:text-white">
                    <flux:icon.arrow-path class="size-4 text-sky-500 dark:text-sky-400" />
                    {{ __('Importações recentes') }}
                </p>
                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('Atualiza automaticamente') }}</span>
            </div>

            <div class="flex flex-col">
                @foreach ($this->importacoes as $importacao)
                    @php
                        $statusStyles = [
                            'pendente' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
                            'processando' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                            'concluido' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                            'falhou' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                        ];
                        $statusLabels = [
                            'pendente' => __('Pendente'),
                            'processando' => __('Processando'),
                            'concluido' => __('Concluído'),
                            'falhou' => __('Falhou'),
                        ];
                        $statusStyle = $statusStyles[$importacao->status] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';
                        $statusLabel = $statusLabels[$importacao->status] ?? $importacao->status;

                        $progresso = $importacao->total_linhas !== null && $importacao->total_linhas > 0
                            ? (int) round(($importacao->importadas / $importacao->total_linhas) * 100)
                            : ($importacao->status === 'concluido' ? 100 : 0);
                    @endphp
                    <div wire:key="import-{{ $importacao->id }}" class="border-b border-zinc-100 px-5 py-4 last:border-b-0 dark:border-white/5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $importacao->nome_original }}</p>
                                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                    {{ $importacao->created_at->format('d/m/Y H:i') }} · {{ $importacao->user?->name }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                @if ($importacao->status === 'processando')
                                    <svg class="mr-1 size-3 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                @endif
                                {{ $statusLabel }}
                            </span>
                        </div>

                        @if ($importacao->status === 'concluido' || $importacao->status === 'processando')
                            <div class="mt-3 flex items-center gap-3">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                                    <div
                                        class="h-full rounded-full {{ $importacao->status === 'concluido' ? 'bg-emerald-500' : 'bg-sky-500' }} transition-all duration-500"
                                        style="width: {{ min(100, $progresso) }}%"
                                    ></div>
                                </div>
                                <span class="shrink-0 text-xs tabular-nums text-zinc-400 dark:text-zinc-500">{{ $progresso }}%</span>
                            </div>
                        @endif

                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                            @if ($importacao->total_linhas !== null)
                                <span>{{ $importacao->total_linhas }} {{ __('linhas') }}</span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.check-circle class="size-3.5 text-emerald-500 dark:text-emerald-400" />
                                {{ $importacao->importadas }} {{ __('importadas') }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.x-circle class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                                {{ $importacao->ignoradas }} {{ __('ignoradas') }}
                            </span>
                        </div>

                        @if ($importacao->status === 'falhou' && $importacao->erro)
                            <p class="mt-2 rounded-lg bg-rose-500/10 px-3 py-2 text-xs leading-relaxed text-rose-700 dark:text-rose-400">
                                {{ $importacao->erro }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-zinc-200/40 blur-2xl dark:bg-white/5"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de radio links') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.radio class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Links ativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['ativos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.bolt class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-sky-200/40 blur-2xl dark:bg-sky-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Fabricantes') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $this->stats['fabricantes'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 transition-transform group-hover:scale-105 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.cpu-chip class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-violet-200/40 blur-2xl dark:bg-violet-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Estações conectadas') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-violet-600 dark:text-violet-400">{{ $this->stats['estacoes'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 transition-transform group-hover:scale-105 dark:bg-violet-400/10 dark:text-violet-400">
                    <flux:icon.signal class="size-5" />
                </div>
            </div>
        </div>
    </section>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto_auto_auto] sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por código, estação, fabricante...')"
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="filtroFabricante" class="w-full sm:w-44">
                <flux:select.option value="">{{ __('Todos os fabricantes') }}</flux:select.option>
                @foreach ($this->fabricantes as $fabricante)
                    <flux:select.option :value="$fabricante">{{ $fabricante }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-40">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                @foreach ($this->statuses as $status)
                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="perPage" class="w-full sm:w-32" :label="__('Itens por página')">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
                <flux:select.option value="100">100</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($radioLinks->total() > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $radioLinks->firstItem() }}-{{ $radioLinks->lastItem() }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $radioLinks->total() }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            <div class="flex items-center gap-3">
                <div wire:loading.delay wire:target="search,filtroStatus,filtroFabricante,sortBy,perPage" class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                    <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('Carregando...') }}
                </div>

                @if ($search !== '' || $filtroStatus !== '' || $filtroFabricante !== '')
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

        <div wire:loading.class="opacity-40" wire:target="search,filtroStatus,filtroFabricante,sortBy,perPage" class="transition-opacity duration-200">
            {{-- Column Headers (sortable) --}}
            @if ($radioLinks->total() > 0)
                <div class="hidden items-center gap-4 border-b border-zinc-100 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 md:flex dark:border-white/5 dark:text-zinc-500">
                    <button type="button" wire:click="sortBy('codigo')" class="group/col flex min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Radio Link') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'codigo'])
                    </button>

                    <button type="button" wire:click="sortBy('estacao_a_id')" class="group/col hidden min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 sm:flex dark:hover:text-zinc-200">
                        {{ __('Estações') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'estacao_a_id'])
                    </button>

                    <button type="button" wire:click="sortBy('frequencia')" class="group/col hidden w-32 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Frequência') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'frequencia'])
                    </button>

                    <button type="button" wire:click="sortBy('capacidade')" class="group/col hidden w-28 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Capacidade') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'capacidade'])
                    </button>

                    <button type="button" wire:click="sortBy('data_ativacao')" class="group/col hidden w-24 shrink-0 cursor-pointer items-center justify-end gap-1 text-right transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Ativação') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'data_ativacao'])
                    </button>

                    <button type="button" wire:click="sortBy('status')" class="group/col flex w-28 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Status') }}
                        @include('livewire.radio-links.partials.sort-indicator', ['field' => 'status'])
                    </button>

                    <div class="w-24 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>
            @endif

            @forelse ($radioLinks as $radioLink)
                @php
                    $avatarTints = [
                        'bg-sky-500/15 text-sky-700 dark:text-sky-300',
                        'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
                        'bg-violet-500/15 text-violet-700 dark:text-violet-300',
                        'bg-amber-500/15 text-amber-700 dark:text-amber-300',
                        'bg-rose-500/15 text-rose-700 dark:text-rose-300',
                        'bg-teal-500/15 text-teal-700 dark:text-teal-300',
                    ];
                    $tint = $avatarTints[$radioLink->id % count($avatarTints)];

                    $statusStyles = [
                        'Em implantação' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'Ativo' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                        'Inativo' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
                        'Desativado' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                        'Cancelado' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                    ];
                    $statusStyle = $statusStyles[$radioLink->status] ?? 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300';
                @endphp

                {{-- Desktop row --}}
                <div class="group hidden items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-all duration-200 last:border-b-0 hover:bg-zinc-50/80 md:flex dark:border-white/5 dark:hover:bg-white/[0.02]">
                    {{-- Codigo + nome --}}
                    <a href="{{ route('radio-links.show', $radioLink) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                            {{ \Illuminate\Support\Str::limit($radioLink->codigo, 5, '') }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $radioLink->codigo }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $radioLink->nome ?: __('Sem nome') }}</p>
                        </div>
                    </a>

                    {{-- Estações --}}
                    <div class="hidden min-w-0 flex-1 flex-col items-start gap-1 sm:flex">
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                            <flux:icon.arrow-right class="size-3.5 text-sky-500 dark:text-sky-400" />
                            {{ $radioLink->estacaoA->site_id }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                            <flux:icon.arrow-right class="size-3.5 text-violet-500 dark:text-violet-400" />
                            {{ $radioLink->estacaoB->site_id }}
                        </span>
                    </div>

                    {{-- Frequência --}}
                    <div class="hidden w-32 shrink-0 lg:block">
                        @if ($radioLink->frequencia !== null)
                            <span class="text-sm tabular-nums text-zinc-700 dark:text-zinc-300">{{ number_format((float) $radioLink->frequencia, 3, ',', '.') }} GHz</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Capacidade --}}
                    <div class="hidden w-28 shrink-0 md:block">
                        @if ($radioLink->capacidade)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $radioLink->capacidade }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Ativação --}}
                    <div class="hidden w-24 text-right lg:block">
                        @if ($radioLink->data_ativacao)
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $radioLink->data_ativacao->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-28 shrink-0">
                        @if ($radioLink->status)
                            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full bg-current"></span>
                                <span class="truncate">{{ $radioLink->status }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-400 dark:bg-white/5 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex w-24 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <flux:button
                            href="{{ route('radio-links.show', $radioLink) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $radioLink->codigo"
                        />
                        <flux:button
                            href="{{ route('radio-links.edit', $radioLink) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $radioLink->codigo"
                        />
                        <flux:button
                            wire:click="destroy({{ $radioLink->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $radioLink->codigo"
                        />
                    </div>
                </div>

                {{-- Mobile card --}}
                <div class="group border-b border-zinc-100 p-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:hidden dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('radio-links.show', $radioLink) }}" wire:navigate class="flex min-w-0 items-center gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                                {{ \Illuminate\Support\Str::limit($radioLink->codigo, 5, '') }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $radioLink->codigo }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $radioLink->nome ?: __('Sem nome') }}</p>
                            </div>
                        </a>

                        @if ($radioLink->status)
                            <span class="inline-flex max-w-full shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full bg-current"></span>
                                <span class="truncate">{{ $radioLink->status }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1.5">
                            <flux:icon.signal class="size-3.5 shrink-0 text-sky-500 dark:text-sky-400" />
                            <span>{{ $radioLink->estacaoA->site_id }} → {{ $radioLink->estacaoB->site_id }}</span>
                        </span>
                        @if ($radioLink->frequencia !== null)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.speaker-wave class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ number_format((float) $radioLink->frequencia, 3, ',', '.') }} GHz
                            </span>
                        @endif
                        @if ($radioLink->capacidade)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.chart-bar class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ $radioLink->capacidade }}
                            </span>
                        @endif
                        @if ($radioLink->fabricante)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.cpu-chip class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ $radioLink->fabricante }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-1 border-t border-zinc-100 pt-3 dark:border-white/5">
                        <flux:button
                            href="{{ route('radio-links.show', $radioLink) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $radioLink->codigo"
                        />
                        <flux:button
                            href="{{ route('radio-links.edit', $radioLink) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $radioLink->codigo"
                        />
                        <flux:button
                            wire:click="destroy({{ $radioLink->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $radioLink->codigo"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.radio class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhum radio link encontrado') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Tente ajustar sua busca ou os filtros para encontrar o que procura.') }}
                    </p>
                    @if ($search !== '' || $filtroStatus !== '' || $filtroFabricante !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('radio-links.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-5">
                            {{ __('Cadastrar primeiro radio link') }}
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($radioLinks->hasPages())
            <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Mostrando') }} {{ $radioLinks->firstItem() }} {{ __('a') }} {{ $radioLinks->lastItem() }} {{ __('de') }} {{ $radioLinks->total() }} {{ __('resultados') }}
                </div>
                <div class="flex items-center gap-1">
                    @if ($radioLinks->onFirstPage())
                        <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                            <flux:icon.chevron-left variant="micro" />
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage"
                            aria-label="{{ __('Página anterior') }}"
                            class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            <flux:icon.chevron-left variant="micro" />
                        </button>
                    @endif

                    @foreach ($radioLinks->getUrlRange(max(1, $radioLinks->currentPage() - 1), min($radioLinks->lastPage(), $radioLinks->currentPage() + 1)) as $page => $url)
                        @if ($page == $radioLinks->currentPage())
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

                    @if ($radioLinks->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage"
                            aria-label="{{ __('Próxima página') }}"
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
                <flux:heading level="2">{{ __('Importar radio links') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Envie um arquivo Excel (.xlsx) ou CSV com os radio links. A importação roda em segundo plano via fila de jobs, ideal para arquivos com milhares de linhas.') }}
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

            {{-- Modelo de planilha --}}
            <div class="rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                            <flux:icon.document-arrow-down class="size-4.5" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Não sabe por onde começar?') }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Baixe o modelo pronto para preencher.') }}</p>
                        </div>
                    </div>
                    <a
                        href="{{ route('radio-links.importar.modelo') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-violet-500/10 px-3 py-2 text-xs font-semibold text-violet-700 transition-colors hover:bg-violet-500/20 dark:bg-violet-400/10 dark:text-violet-400 dark:hover:bg-violet-400/20"
                    >
                        <flux:icon.arrow-down-tray class="size-3.5" />
                        {{ __('Baixar modelo .xlsx') }}
                    </a>
                </div>

                <div class="mt-3 border-t border-zinc-100 pt-3 dark:border-white/5">
                    <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ __('Colunas esperadas') }}:</p>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach (['codigo', 'nome', 'estacao_a', 'estacao_b', 'frequencia', 'capacidade', 'canal', 'polarizacao', 'fabricante', 'modelo', 'distancia', 'status', 'data_ativacao', 'observacao'] as $coluna)
                            <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-[11px] text-zinc-600 dark:bg-white/10 dark:text-zinc-300">{{ $coluna }}</span>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                        {{ __('Obrigatórias') }}: <strong>codigo</strong>, <strong>estacao_a</strong> e <strong>estacao_b</strong> (Site ID).
                        {{ __('Linhas com código duplicado são atualizadas; estações A/B são resolvidas pelo Site ID.') }}
                    </p>
                </div>
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