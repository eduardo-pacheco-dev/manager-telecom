<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    @php
        $avatarTints = [
            'bg-sky-500/15 text-sky-700 dark:text-sky-300',
            'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
            'bg-violet-500/15 text-violet-700 dark:text-violet-300',
            'bg-amber-500/15 text-amber-700 dark:text-amber-300',
            'bg-rose-500/15 text-rose-700 dark:text-rose-300',
            'bg-teal-500/15 text-teal-700 dark:text-teal-300',
        ];

        $categoriaStyles = [
            'CLT' => 'bg-sky-500/10 text-sky-700 ring-1 ring-sky-500/20 dark:text-sky-400 dark:ring-sky-400/20',
            'PJ' => 'bg-violet-500/10 text-violet-700 ring-1 ring-violet-500/20 dark:text-violet-400 dark:ring-violet-400/20',
            'Freelancer' => 'bg-amber-500/10 text-amber-700 ring-1 ring-amber-500/20 dark:text-amber-400 dark:ring-amber-400/20',
        ];
    @endphp

    {{-- Page header --}}
    <div class="animate-fade-in-up flex flex-col gap-5">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Colaboradores') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="xl" level="1" class="flex items-center gap-3">
                    {{ __('Colaboradores') }}
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-zinc-100 px-2 py-0.5 text-sm font-semibold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                        {{ $this->stats['total'] }}
                    </span>
                </flux:heading>
                <flux:subheading size="lg" class="mt-1">{{ __('Gerencie a equipe e os colaboradores da empresa') }}</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                <flux:button href="{{ route('colaboradores.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Novo Colaborador') }}
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
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de colaboradores') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.users class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Ativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['ativos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.check-circle class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-rose-200/40 blur-2xl dark:bg-rose-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Inativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-rose-600 dark:text-rose-400">{{ $this->stats['inativos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 transition-transform group-hover:scale-105 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.x-circle class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-sky-200/40 blur-2xl dark:bg-sky-400/10"></div>
            <div class="relative flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Departamentos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $this->stats['departamentos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 transition-transform group-hover:scale-105 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.building-office-2 class="size-5" />
                </div>
            </div>
        </div>
    </section>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
            <div class="lg:min-w-64 lg:flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('Buscar por nome, email, cargo ou CPF...')"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <div class="grid gap-2 sm:grid-cols-2 lg:flex lg:items-center">
                <flux:select wire:model.live="filtroDepartamento" class="w-full sm:w-48">
                    <flux:select.option value="">{{ __('Todos os departamentos') }}</flux:select.option>
                    @foreach ($this->departamentos as $departamento)
                        <flux:select.option :value="$departamento">{{ $departamento }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filtroCategoria" class="w-full sm:w-40">
                    <flux:select.option value="">{{ __('Todas as categorias') }}</flux:select.option>
                    @foreach ($this->categorias as $categoria)
                        <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filtroStatus" class="w-full sm:w-36">
                    <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                    <flux:select.option value="ativo">{{ __('Ativos') }}</flux:select.option>
                    <flux:select.option value="inativo">{{ __('Inativos') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="perPage" class="w-full sm:w-32" :label="__('Itens por página')">
                    <flux:select.option value="10">10</flux:select.option>
                    <flux:select.option value="25">25</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                    <flux:select.option value="100">100</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($colaboradores->total() > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $colaboradores->firstItem() }}-{{ $colaboradores->lastItem() }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $colaboradores->total() }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            <div class="flex items-center gap-3">
                <div wire:loading.delay wire:target="search,filtroDepartamento,filtroCategoria,filtroStatus,sortBy,perPage" class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                    <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('Carregando...') }}
                </div>

                @if ($search !== '' || $filtroDepartamento !== '' || $filtroCategoria !== '' || $filtroStatus !== '')
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

        <div wire:loading.class="opacity-40" wire:target="search,filtroDepartamento,filtroCategoria,filtroStatus,sortBy,perPage,toggleAtivo,destroy" class="transition-opacity duration-200">
            {{-- Column Headers (sortable) --}}
            @if ($colaboradores->total() > 0)
                <div class="hidden items-center gap-4 border-b border-zinc-100 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 md:flex dark:border-white/5 dark:text-zinc-500">
                    <button type="button" wire:click="sortBy('nome')" class="group/col flex min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Colaborador') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'nome'])
                    </button>

                    <button type="button" wire:click="sortBy('departamento')" class="group/col hidden min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 sm:flex dark:hover:text-zinc-200">
                        {{ __('Departamento / Cargo') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'departamento'])
                    </button>

                    <button type="button" wire:click="sortBy('categoria')" class="group/col hidden w-32 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Categoria') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'categoria'])
                    </button>

                    <button type="button" wire:click="sortBy('data_admissao')" class="group/col hidden w-28 shrink-0 cursor-pointer items-center justify-end gap-1 text-right transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Admissão') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'data_admissao'])
                    </button>

                    <button type="button" wire:click="sortBy('salario')" class="group/col hidden w-32 shrink-0 cursor-pointer items-center justify-end gap-1 text-right transition-colors hover:text-zinc-700 lg:flex dark:hover:text-zinc-200">
                        {{ __('Salário') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'salario'])
                    </button>

                    <button type="button" wire:click="sortBy('ativo')" class="group/col flex w-24 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Status') }}
                        @include('livewire.colaboradores.partials.sort-indicator', ['field' => 'ativo'])
                    </button>

                    <div class="w-28 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>
            @endif

            @forelse ($colaboradores as $colaborador)
                @php
                    $tint = $avatarTints[$colaborador->id % count($avatarTints)];
                    $categoriaStyle = $categoriaStyles[$colaborador->categoria] ?? 'bg-zinc-100 text-zinc-600 ring-1 ring-zinc-200/60 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10';
                @endphp

                {{-- Desktop row --}}
                <div class="group hidden items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-all duration-200 last:border-b-0 hover:bg-zinc-50/80 md:flex dark:border-white/5 dark:hover:bg-white/[0.02]">
                    {{-- Avatar + Name --}}
                    <a href="{{ route('colaboradores.show', $colaborador) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-semibold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                            {{ \Illuminate\Support\Str::initials($colaborador->nome, true) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $colaborador->nome }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $colaborador->email }}</p>
                        </div>
                    </a>

                    {{-- Department + Role --}}
                    <div class="hidden min-w-0 flex-1 flex-col items-start gap-1 sm:flex">
                        @if ($colaborador->departamento)
                            <span class="inline-flex max-w-full items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                <span class="truncate">{{ $colaborador->departamento }}</span>
                            </span>
                        @endif
                        @if ($colaborador->cargo)
                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $colaborador->cargo }}</span>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div class="hidden w-32 shrink-0 lg:block">
                        @if ($colaborador->categoria)
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $categoriaStyle }}">
                                {{ $colaborador->categoria }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Admission --}}
                    <div class="hidden w-28 shrink-0 text-right md:block">
                        @if ($colaborador->data_admissao)
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $colaborador->data_admissao->format('d/m/Y') }}</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Salary --}}
                    <div class="hidden w-32 shrink-0 text-right lg:block">
                        @if ($colaborador->salario)
                            <span class="text-xs font-medium tabular-nums text-zinc-700 dark:text-zinc-300">
                                {{ \Illuminate\Support\Number::currency((float) $colaborador->salario, 'BRL') }}
                            </span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-24 shrink-0">
                        @if ($colaborador->ativo)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-current"></span>
                                {{ __('Ativo') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-medium text-rose-700 dark:text-rose-400">
                                <span class="size-1.5 rounded-full bg-current"></span>
                                {{ __('Inativo') }}
                            </span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex w-28 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <flux:button
                            href="{{ route('colaboradores.show', $colaborador) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            href="{{ route('colaboradores.edit', $colaborador) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            wire:click="toggleAtivo({{ $colaborador->id }})"
                            size="sm"
                            variant="ghost"
                            icon="{{ $colaborador->ativo ? 'x-mark' : 'check' }}"
                            :title="$colaborador->ativo ? __('Desativar') : __('Ativar')"
                            :aria-label="$colaborador->ativo ? __('Desativar') . ' ' . $colaborador->nome : __('Ativar') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            wire:click="$set('colaboradorParaExcluir', {{ $colaborador->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $colaborador->nome"
                        />
                    </div>
                </div>

                {{-- Mobile card --}}
                <div class="group border-b border-zinc-100 p-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:hidden dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('colaboradores.show', $colaborador) }}" wire:navigate class="flex min-w-0 items-center gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-semibold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                                {{ \Illuminate\Support\Str::initials($colaborador->nome, true) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $colaborador->nome }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $colaborador->email }}</p>
                            </div>
                        </a>

                        @if ($colaborador->ativo)
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-current"></span>
                                {{ __('Ativo') }}
                            </span>
                        @else
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-medium text-rose-700 dark:text-rose-400">
                                <span class="size-1.5 rounded-full bg-current"></span>
                                {{ __('Inativo') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                        @if ($colaborador->departamento)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $colaborador->departamento }}
                            </span>
                        @endif
                        @if ($colaborador->categoria)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 font-medium {{ $categoriaStyle }}">
                                {{ $colaborador->categoria }}
                            </span>
                        @endif
                        @if ($colaborador->data_admissao)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.calendar-days class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ $colaborador->data_admissao->format('d/m/Y') }}
                            </span>
                        @endif
                        @if ($colaborador->salario)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.banknotes class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                                {{ \Illuminate\Support\Number::currency((float) $colaborador->salario, 'BRL') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-1 border-t border-zinc-100 pt-3 dark:border-white/5">
                        <flux:button
                            href="{{ route('colaboradores.show', $colaborador) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            href="{{ route('colaboradores.edit', $colaborador) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            wire:click="toggleAtivo({{ $colaborador->id }})"
                            size="sm"
                            variant="ghost"
                            icon="{{ $colaborador->ativo ? 'x-mark' : 'check' }}"
                            :title="$colaborador->ativo ? __('Desativar') : __('Ativar')"
                            :aria-label="$colaborador->ativo ? __('Desativar') . ' ' . $colaborador->nome : __('Ativar') . ' ' . $colaborador->nome"
                        />
                        <flux:button
                            wire:click="$set('colaboradorParaExcluir', {{ $colaborador->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $colaborador->nome"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.user-group class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhum colaborador encontrado') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Tente ajustar sua busca ou os filtros para encontrar o que procura.') }}
                    </p>
                    @if ($search !== '' || $filtroDepartamento !== '' || $filtroCategoria !== '' || $filtroStatus !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('colaboradores.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-5">
                            {{ __('Cadastrar primeiro colaborador') }}
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($colaboradores->hasPages())
            <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Mostrando') }} {{ $colaboradores->firstItem() }} {{ __('a') }} {{ $colaboradores->lastItem() }} {{ __('de') }} {{ $colaboradores->total() }} {{ __('resultados') }}
                </div>
                <div class="flex items-center gap-1">
                    @if ($colaboradores->onFirstPage())
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

                    @foreach ($colaboradores->getUrlRange(max(1, $colaboradores->currentPage() - 1), min($colaboradores->lastPage(), $colaboradores->currentPage() + 1)) as $page => $url)
                        @if ($page == $colaboradores->currentPage())
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

                    @if ($colaboradores->hasMorePages())
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

    {{-- Delete confirmation modal --}}
    @php $colaboradorAlvo = $this->colaboradorAlvo; @endphp

    <flux:modal wire:model="colaboradorParaExcluir" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Excluir colaborador?') }}</flux:heading>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Esta ação não pode ser desfeita. O colaborador :nome será removido permanentemente.', ['nome' => $colaboradorAlvo?->nome ?? '']) }}
                </p>
            </div>

            <div class="flex gap-3">
                <flux:button variant="ghost" wire:click="$set('colaboradorParaExcluir', null)" class="w-full">
                    {{ __('Cancelar') }}
                </flux:button>

                @if ($colaboradorAlvo)
                    <flux:button variant="danger" type="button" wire:click="destroy({{ $colaboradorAlvo->id }})" class="w-full">
                        {{ __('Excluir') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
</div>