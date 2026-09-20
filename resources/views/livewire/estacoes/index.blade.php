<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <div class="animate-fade-in-up flex flex-col gap-5">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Estações') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="xl" level="1" class="flex items-center gap-3">
                    {{ __('Estações') }}
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-zinc-100 px-2 py-0.5 text-sm font-semibold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                        {{ $this->stats['total'] }}
                    </span>
                </flux:heading>
                <flux:subheading size="lg" class="mt-1">{{ __('Gerencie as estações de telecomunicações') }}</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Nova Estação') }}
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-zinc-200/40 blur-2xl dark:bg-white/5"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de estações') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.signal class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Tecnologias') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['tecnologias'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.cpu-chip class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-sky-200/40 blur-2xl dark:bg-sky-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Municípios') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $this->stats['municipios'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 transition-transform group-hover:scale-105 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.map-pin class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-violet-200/40 blur-2xl dark:bg-violet-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Tipos de elemento') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-violet-600 dark:text-violet-400">{{ $this->stats['elementos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 transition-transform group-hover:scale-105 dark:bg-violet-400/10 dark:text-violet-400">
                    <flux:icon.radio class="size-5" />
                </div>
            </div>
        </div>
    </section>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto_auto_auto] sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por Site ID, elemento, município ou Endereço ID...')"
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="filtroTipoElemento" class="w-full sm:w-44">
                <flux:select.option value="">{{ __('Todos os elementos') }}</flux:select.option>
                @foreach ($this->tiposElemento as $tipoElemento)
                    <flux:select.option :value="$tipoElemento">{{ $tipoElemento }}</flux:select.option>
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
                @if ($estacoes->total() > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $estacoes->firstItem() }}-{{ $estacoes->lastItem() }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $estacoes->total() }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            <div class="flex items-center gap-3">
                <div wire:loading.delay wire:target="search,filtroTipoElemento,filtroStatus,sortBy,perPage" class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                    <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('Carregando...') }}
                </div>

                @if ($search !== '' || $filtroTipoElemento !== '' || $filtroStatus !== '')
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

        <div wire:loading.class="opacity-40" wire:target="search,filtroTipoElemento,filtroStatus,sortBy,perPage" class="transition-opacity duration-200">
            {{-- Column Headers (sortable) --}}
            @if ($estacoes->total() > 0)
                <div class="hidden items-center gap-4 border-b border-zinc-100 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 md:flex dark:border-white/5 dark:text-zinc-500">
                    <button type="button" wire:click="sortBy('site_id')" class="group/col flex min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Estação') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'site_id'])
                    </button>

                    <button type="button" wire:click="sortBy('tipo_elemento')" class="group/col hidden min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 sm:flex dark:hover:text-zinc-200">
                        {{ __('Elemento / Tecnologia') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'tipo_elemento'])
                    </button>

                    <button type="button" wire:click="sortBy('classificacao')" class="group/col hidden w-32 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Classificação') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'classificacao'])
                    </button>

                    <button type="button" wire:click="sortBy('municipio')" class="group/col hidden min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Município') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'municipio'])
                    </button>

                    <button type="button" wire:click="sortBy('data_aquisicao')" class="group/col hidden w-24 shrink-0 cursor-pointer items-center justify-end gap-1 text-right transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Aquisição') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'data_aquisicao'])
                    </button>

                    <button type="button" wire:click="sortBy('status')" class="group/col flex w-28 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Status') }}
                        @include('livewire.estacoes.partials.sort-indicator', ['field' => 'status'])
                    </button>

                    <div class="w-24 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>
            @endif

            @forelse ($estacoes as $estacao)
                @php
                    $avatarTints = [
                        'bg-sky-500/15 text-sky-700 dark:text-sky-300',
                        'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
                        'bg-violet-500/15 text-violet-700 dark:text-violet-300',
                        'bg-amber-500/15 text-amber-700 dark:text-amber-300',
                        'bg-rose-500/15 text-rose-700 dark:text-rose-300',
                        'bg-teal-500/15 text-teal-700 dark:text-teal-300',
                    ];
                    $tint = $avatarTints[$estacao->id % count($avatarTints)];

                    $statusStyles = [
                        'CANDIDATO A' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'Aquisitado' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                        'Adquirido' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                        'Em construção' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                        'Ativo' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                        'Inativo' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
                        'Desativado' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                        'Cancelado' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
                    ];
                    $statusStyle = $statusStyles[$estacao->status] ?? 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300';

                    $classificacaoStyles = [
                        'ACESSO' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
                        'RANSHARING' => 'bg-violet-500/10 text-violet-700 dark:text-violet-400',
                        'BACKHAUL' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
                        'TRANSPORTE' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
                    ];
                    $classificacaoStyle = $classificacaoStyles[$estacao->classificacao] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';
                @endphp

                {{-- Desktop row --}}
                <div class="group hidden items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-all duration-200 last:border-b-0 hover:bg-zinc-50/80 md:flex dark:border-white/5 dark:hover:bg-white/[0.02]">
                    {{-- Site ID + element --}}
                    <a href="{{ route('estacoes.show', $estacao) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                            {{ \Illuminate\Support\Str::limit($estacao->site_id, 5, '') }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $estacao->site_id }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->endereco_id ?: __('Sem Endereço ID') }}</p>
                        </div>
                    </a>

                    {{-- Element + technology --}}
                    <div class="hidden min-w-0 flex-1 flex-col items-start gap-1 sm:flex">
                        @if ($estacao->tipo_elemento)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $estacao->tipo_elemento }}
                            </span>
                        @endif
                        @if ($estacao->tecnologia)
                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->tecnologia }}</span>
                        @endif
                    </div>

                    {{-- Classification --}}
                    <div class="hidden w-32 shrink-0 lg:block">
                        @if ($estacao->classificacao)
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $classificacaoStyle }}">
                                {{ $estacao->classificacao }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Municipality --}}
                    <div class="hidden min-w-0 flex-1 flex-col items-start gap-1 md:flex">
                        @if ($estacao->municipio)
                            <span class="truncate text-sm text-zinc-700 dark:text-zinc-300">{{ $estacao->municipio }}</span>
                        @endif
                        @if ($estacao->estado)
                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->estado }} · {{ $estacao->regional ?: '—' }}</span>
                        @endif
                    </div>

                    {{-- Acquisition --}}
                    <div class="hidden w-24 text-right lg:block">
                        @if ($estacao->data_aquisicao)
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->data_aquisicao->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-28 shrink-0">
                        @if ($estacao->status)
                            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full bg-current"></span>
                                <span class="truncate">{{ $estacao->status }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-400 dark:bg-white/5 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex w-24 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <flux:button
                            href="{{ route('estacoes.show', $estacao) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $estacao->site_id"
                        />
                        <flux:button
                            href="{{ route('estacoes.edit', $estacao) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $estacao->site_id"
                        />
                        <flux:button
                            wire:click="destroy({{ $estacao->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $estacao->site_id"
                        />
                    </div>
                </div>

                {{-- Mobile card --}}
                <div class="group border-b border-zinc-100 p-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:hidden dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('estacoes.show', $estacao) }}" wire:navigate class="flex min-w-0 items-center gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                                {{ \Illuminate\Support\Str::limit($estacao->site_id, 5, '') }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $estacao->site_id }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->endereco_id ?: __('Sem Endereço ID') }}</p>
                            </div>
                        </a>

                        @if ($estacao->status)
                            <span class="inline-flex max-w-full shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                                <span class="size-1.5 shrink-0 rounded-full bg-current"></span>
                                <span class="truncate">{{ $estacao->status }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                        @if ($estacao->tipo_elemento)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $estacao->tipo_elemento }}
                            </span>
                        @endif
                        @if ($estacao->tecnologia)
                            <span>{{ $estacao->tecnologia }}</span>
                        @endif
                        @if ($estacao->classificacao)
                            <span class="inline-flex items-center gap-1.5">
                                <flux:icon.tag class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                                <span class="font-medium {{ str_contains($classificacaoStyle, 'text-sky') ? 'text-sky-600 dark:text-sky-400' : (str_contains($classificacaoStyle, 'text-violet') ? 'text-violet-600 dark:text-violet-400' : (str_contains($classificacaoStyle, 'text-emerald') ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400')) }}">
                                    {{ $estacao->classificacao }}
                                </span>
                            </span>
                        @endif
                        @if ($estacao->municipio)
                            <span class="inline-flex min-w-0 items-center gap-1">
                                <flux:icon.map-pin class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                <span class="truncate">{{ $estacao->municipio }}{{ $estacao->estado ? ' - '.$estacao->estado : '' }}</span>
                            </span>
                        @endif
                        @if ($estacao->data_aquisicao)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.calendar-days class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ $estacao->data_aquisicao->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-1 border-t border-zinc-100 pt-3 dark:border-white/5">
                        <flux:button
                            href="{{ route('estacoes.show', $estacao) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $estacao->site_id"
                        />
                        <flux:button
                            href="{{ route('estacoes.edit', $estacao) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $estacao->site_id"
                        />
                        <flux:button
                            wire:click="destroy({{ $estacao->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $estacao->site_id"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.signal class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhuma estação encontrada') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Tente ajustar sua busca ou os filtros para encontrar o que procura.') }}
                    </p>
                    @if ($search !== '' || $filtroTipoElemento !== '' || $filtroStatus !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-5">
                            {{ __('Cadastrar primeira estação') }}
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($estacoes->hasPages())
            <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Mostrando') }} {{ $estacoes->firstItem() }} {{ __('a') }} {{ $estacoes->lastItem() }} {{ __('de') }} {{ $estacoes->total() }} {{ __('resultados') }}
                </div>
                <div class="flex items-center gap-1">
                    @if ($estacoes->onFirstPage())
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

                    @foreach ($estacoes->getUrlRange(max(1, $estacoes->currentPage() - 1), min($estacoes->lastPage(), $estacoes->currentPage() + 1)) as $page => $url)
                        @if ($page == $estacoes->currentPage())
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

                    @if ($estacoes->hasMorePages())
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
</div>