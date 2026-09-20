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
        <x-ui.stat-card
            :label="__('Total de ordens')"
            :value="$total"
            icon="clipboard-document-list"
            color="zinc"
            :progress="100"
            :footnote="__('Inventário completo')"
        />

        <x-ui.stat-card
            :label="__('Em aberto')"
            :value="$abertas"
            icon="clock"
            color="sky"
            :progress="$pctAbertas"
            :footnote="$pctAbertas.'% '.__('do total')"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Concluídas')"
            :value="$concluidas"
            icon="check-circle"
            color="emerald"
            :progress="$pctConcluidas"
            :footnote="$pctConcluidas.'% '.__('do total')"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Urgentes')"
            :value="$urgentes"
            icon="exclamation-triangle"
            color="rose"
            :progress="$pctUrgentes"
            :footnote="$pctUrgentes.'% '.__('do total')"
            delay="190ms"
        />
    </section>

    {{-- Importações: notifica via toast --}}
    <div wire:poll.5s="verificarImportacoes" class="hidden" aria-hidden="true"></div>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 260ms">
        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto_auto_auto] sm:items-center">
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
                <x-ordens-servico.row :ordem="$ordem" />
            @empty
                <x-ui.empty
                    icon="clipboard-document-list"
                    :title="__('Nenhuma ordem de serviço encontrada')"
                    :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                >
                    @if ($search !== '' || $filtroStatus !== '' || $filtroTipo !== '' || $filtroPrioridade !== '')
                        <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                            {{ __('Limpar filtros') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('ordens-servico.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                            {{ __('Cadastrar primeira ordem') }}
                        </flux:button>
                    @endif
                </x-ui.empty>
            @endforelse
        </div>

        {{-- Pagination --}}
        <x-ui.pagination :paginator="$ordensServico" show-per-page :per-page="$perPage" />
    </div>

    {{-- Modal de importação --}}
    <x-ordens-servico.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />
</div>