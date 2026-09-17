<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <div class="animate-fade-in-up flex flex-col gap-5">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Serviços') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="xl" level="1" class="flex items-center gap-3">
                    {{ __('Serviços') }}
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-zinc-100 px-2 py-0.5 text-sm font-semibold text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                        {{ $this->stats['total'] }}
                    </span>
                </flux:heading>
                <flux:subheading size="lg" class="mt-1">{{ __('Gerencie os serviços da empresa') }}</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                <flux:button href="{{ route('servicos.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Novo Serviço') }}
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de serviços') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.wrench-screwdriver class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Ativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['ativos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.check-circle class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Inativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-rose-600 dark:text-rose-400">{{ $this->stats['inativos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.x-circle class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Categorias') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $this->stats['categorias'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.tag class="size-5" />
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up flex flex-col gap-3 lg:flex-row lg:items-center" style="animation-delay: 230ms">
        <div class="grid flex-1 gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por nome, código ou categoria...')"
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="filtroCategoria" class="w-full sm:w-52">
                <flux:select.option value="">{{ __('Todas as categorias') }}</flux:select.option>
                @foreach ($this->categorias as $categoria)
                    <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-40">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                <flux:select.option value="ativo">{{ __('Ativos') }}</flux:select.option>
                <flux:select.option value="inativo">{{ __('Inativos') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($servicos->total() > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $servicos->firstItem() }}-{{ $servicos->lastItem() }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $servicos->total() }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            @if ($search !== '' || $filtroCategoria !== '' || $filtroStatus !== '')
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

        <div wire:loading.class="opacity-40" wire:target="search,filtroCategoria,filtroStatus,toggleAtivo" class="transition-opacity duration-200">
            {{-- Column Headers --}}
            @if ($servicos->total() > 0)
                <div class="flex items-center gap-4 border-b border-zinc-100 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:text-zinc-500">
                    <div class="min-w-0 flex-1">{{ __('Serviço') }}</div>
                    <div class="hidden min-w-0 flex-1 sm:block">{{ __('Categoria / Descrição') }}</div>
                    <div class="hidden w-28 text-right md:block">{{ __('Preço') }}</div>
                    <div class="w-20 shrink-0">{{ __('Status') }}</div>
                    <div class="w-24 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>
            @endif

            @forelse ($servicos as $servico)
                @php
                    $avatarTints = [
                        'bg-sky-500/15 text-sky-700 dark:text-sky-300',
                        'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
                        'bg-violet-500/15 text-violet-700 dark:text-violet-300',
                        'bg-amber-500/15 text-amber-700 dark:text-amber-300',
                        'bg-rose-500/15 text-rose-700 dark:text-rose-300',
                        'bg-teal-500/15 text-teal-700 dark:text-teal-300',
                    ];
                    $tint = $avatarTints[$servico->id % count($avatarTints)];
                @endphp

                <div class="group flex items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-all duration-200 last:border-b-0 hover:bg-zinc-50/80 dark:border-white/5 dark:hover:bg-white/[0.02]">
                    {{-- Avatar + Name --}}
                    <a href="{{ route('servicos.show', $servico) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-semibold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                            {{ \Illuminate\Support\Str::initials($servico->nome, true) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $servico->nome }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $servico->codigo ?? '—' }}</p>
                        </div>
                    </a>

                    {{-- Category + Description --}}
                    <div class="hidden min-w-0 flex-1 flex-col items-start gap-1 sm:flex">
                        @if ($servico->categoria)
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                {{ $servico->categoria }}
                            </span>
                        @endif
                        @if ($servico->descricao)
                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $servico->descricao }}</span>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="hidden w-28 text-right md:block">
                        @if ($servico->preco !== null)
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">R$ {{ number_format($servico->preco, 2, ',', '.') }}</span>
                        @else
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="w-20 shrink-0">
                        @if ($servico->ativo)
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
                    <div class="flex shrink-0 items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <flux:button
                            href="{{ route('servicos.show', $servico) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                            :title="__('Ver detalhes')"
                            :aria-label="__('Ver detalhes de') . ' ' . $servico->nome"
                        />
                        <flux:button
                            href="{{ route('servicos.edit', $servico) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $servico->nome"
                        />
                        <flux:button
                            wire:click="toggleAtivo({{ $servico->id }})"
                            size="sm"
                            variant="ghost"
                            icon="{{ $servico->ativo ? 'x-mark' : 'check' }}"
                            :title="$servico->ativo ? __('Desativar') : __('Ativar')"
                            :aria-label="$servico->ativo ? __('Desativar') . ' ' . $servico->nome : __('Ativar') . ' ' . $servico->nome"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.wrench-screwdriver class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhum serviço encontrado') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Tente ajustar sua busca ou os filtros para encontrar o que procura.') }}
                    </p>
                    @if ($search !== '' || $filtroCategoria !== '' || $filtroStatus !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('servicos.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-5">
                            {{ __('Cadastrar primeiro serviço') }}
                        </flux:button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($servicos->hasPages())
            <div class="flex items-center justify-between border-t border-zinc-200 px-5 py-3 dark:border-white/10">
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Mostrando') }} {{ $servicos->firstItem() }} {{ __('a') }} {{ $servicos->lastItem() }} {{ __('de') }} {{ $servicos->total() }} {{ __('resultados') }}
                </div>
                <div class="flex items-center gap-1">
                    @if ($servicos->onFirstPage())
                        <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                            <flux:icon.chevron-left variant="micro" />
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage"
                            class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            <flux:icon.chevron-left variant="micro" />
                        </button>
                    @endif

                    @foreach ($servicos->getUrlRange(max(1, $servicos->currentPage() - 1), min($servicos->lastPage(), $servicos->currentPage() + 1)) as $page => $url)
                        @if ($page == $servicos->currentPage())
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

                    @if ($servicos->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage"
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