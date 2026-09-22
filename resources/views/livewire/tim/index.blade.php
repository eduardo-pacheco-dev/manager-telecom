<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Projetos TIM Implantação RF')"
        :subtitle="__('Gerencie os projetos de implantação TIM')"
        :badge="$this->stats['total']"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM Implantação RF'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Novo Projeto') }}
        </flux:button>
    </x-ui.page-header>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <x-ui.stat-card
            :label="__('Total de projetos')"
            :value="$this->stats['total']"
            icon="folder"
            color="zinc"
        />

        <x-ui.stat-card
            :label="__('Ativos')"
            :value="$this->stats['ativos']"
            icon="check-circle"
            color="emerald"
            :progress="$this->stats['total'] > 0 ? ($this->stats['ativos'] / $this->stats['total']) * 100 : 0"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('OS vinculadas')"
            :value="$this->stats['ordens']"
            icon="clipboard-document-list"
            color="sky"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Concluídos')"
            :value="$this->stats['concluidos']"
            icon="flag"
            color="violet"
            :progress="$this->stats['total'] > 0 ? ($this->stats['concluidos'] / $this->stats['total']) * 100 : 0"
            delay="190ms"
        />
    </section>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
        <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
            <div class="lg:min-w-64 lg:flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('Buscar por código, nome ou descrição...')"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                <flux:select wire:model.live="filtroStatus" class="w-full sm:w-44">
                    <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                    @foreach (\App\Models\TimProjeto::STATUS as $status)
                        <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroStatus !== '';
    @endphp

    <x-ui.table
        :total="$projetos->total()"
        :first-item="$projetos->firstItem()"
        :last-item="$projetos->lastItem()"
        loading-targets="search,filtroStatus,sortBy,perPage,destroy"
        :has-filters="$temFiltros"
        :min-width="'64rem'"
        delay="280ms"
        :selected-label="count($this->selecionados) > 0 ? count($this->selecionados).' '.__('selecionado(s)') : null"
    >
        <x-slot:headerActions>
            @if (count($this->selecionados) === 0)
                <flux:button
                    wire:click="exportarTodos"
                    wire:loading.attr="disabled"
                    wire:target="exportarTodos"
                    size="sm"
                    variant="ghost"
                    icon="arrow-down-tray"
                    :title="__('Exportar todos')"
                >
                    <span wire:loading.remove wire:target="exportarTodos">{{ __('Exportar') }}</span>
                    <span wire:loading wire:target="exportarTodos">{{ __('Exportando...') }}</span>
                </flux:button>
            @endif
        </x-slot:headerActions>

        @if (count($this->selecionados) > 0)
            <x-slot:selectedActions>
                <flux:button
                    wire:click="exportarSelecionados"
                    wire:loading.attr="disabled"
                    wire:target="exportarSelecionados"
                    size="sm"
                    variant="subtle"
                    icon="arrow-down-tray"
                >
                    {{ __('Exportar') }}
                </flux:button>
                <flux:button wire:click="excluirSelecionados" size="sm" variant="danger" icon="trash">
                    {{ __('Excluir') }}
                </flux:button>
            </x-slot:selectedActions>
        @endif

        <x-slot:header>
            @if ($projetos->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        @checked(count(array_intersect($this->selecionados, $projetos->pluck('id')->all())) === $projetos->count())
                        aria-label="{{ __('Selecionar todos') }}"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="codigo" :label="__('Projeto')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    {{ __('Cliente') }}
                </th>
                <th scope="col" class="px-4 py-3 text-right">
                    {{ __('OS vinculadas') }}
                </th>
                <th scope="col" class="px-4 py-3 text-right">
                    <x-ui.sortable-header field="data_inicio" :label="__('Início')" align="right" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="ativo" :label="__('Situação')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($projetos as $projeto)
            @php
                $statusStyles = [
                    'Planejamento' => ['bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'bg-zinc-400 dark:bg-zinc-500'],
                    'Em andamento' => ['bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'bg-sky-500 dark:bg-sky-400'],
                    'Pausado' => ['bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'bg-amber-500 dark:bg-amber-400'],
                    'Concluído' => ['bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'bg-emerald-500 dark:bg-emerald-400'],
                    'Cancelado' => ['bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'bg-rose-500 dark:bg-rose-400'],
                ];
                $statusStyle = $statusStyles[$projeto->status] ?? ['bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'bg-zinc-400 dark:bg-zinc-500'];
            @endphp

            <tr wire:key="tim-projeto-{{ $projeto->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
                <td class="whitespace-nowrap px-5 py-3.5 align-middle">
                    <input
                        type="checkbox"
                        wire:model.live="selecionados"
                        value="{{ $projeto->id }}"
                        aria-label="{{ __('Selecionar') . ' ' . $projeto->codigo }}"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 align-middle">
                    <a href="{{ route('tim.show', $projeto) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                            <flux:icon.folder class="size-4.5" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">{{ $projeto->codigo }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $projeto->nome }}</p>
                        </div>
                    </a>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 align-middle">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle[0] }}">
                        <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle[1] }}"></span>
                        {{ $projeto->status }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 align-middle">
                    @if ($projeto->cliente)
                        <div class="flex items-center gap-2">
                            <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                                <flux:icon.user class="size-3.5" />
                            </span>
                            <span class="max-w-40 truncate text-sm text-zinc-600 dark:text-zinc-300">{{ $projeto->cliente->nome }}</span>
                        </div>
                    @else
                        <span class="text-sm text-zinc-300 dark:text-zinc-600">—</span>
                    @endif
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
                    <span class="text-sm font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $projeto->ordens_servico_count }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $projeto->data_inicio?->format('d/m/Y') ?: '—' }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 align-middle">
                    @if ($projeto->ativo)
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
                </td>
                <td class="whitespace-nowrap px-5 py-3.5 text-right align-middle">
                    <div class="inline-flex items-center justify-end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $projeto->codigo" />
                            <flux:menu>
                                <flux:menu.radio.group>
                                    <flux:menu.item :href="route('tim.show', $projeto)" icon="eye" wire:navigate>
                                        {{ __('Ver detalhes') }}
                                    </flux:menu.item>
                                    <flux:menu.item :href="route('tim.edit', $projeto)" icon="pencil-square" wire:navigate>
                                        {{ __('Editar') }}
                                    </flux:menu.item>
                                </flux:menu.radio.group>
                                <flux:menu.separator />
                                <flux:menu.radio.group>
                                    <flux:menu.item
                                        as="button"
                                        type="button"
                                        wire:click="$set('projetoParaExcluir', {{ $projeto->id }})"
                                        icon="trash"
                                        variant="danger"
                                    >
                                        {{ __('Excluir') }}
                                    </flux:menu.item>
                                </flux:menu.radio.group>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </td>
            </tr>
        @empty
            <tr wire:key="tim-projeto-empty">
                <td colspan="8">
                    <x-ui.empty
                        icon="folder"
                        :title="__('Nenhum projeto encontrado')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('tim.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeiro projeto') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$projetos" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Delete confirmation modal --}}
    <flux:modal wire:model="projetoParaExcluir" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Excluir projeto?') }}</flux:heading>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Esta ação não pode ser desfeita. O projeto :nome será removido e suas ordens de serviço deixarão de estar vinculadas.', ['nome' => $this->projetoAlvo?->codigo ?? '']) }}
                </p>
            </div>

            <div class="flex gap-3">
                <flux:button variant="ghost" wire:click="$set('projetoParaExcluir', null)" class="w-full">
                    {{ __('Cancelar') }}
                </flux:button>

                @if ($this->projetoAlvo)
                    <flux:button variant="danger" type="button" wire:click="destroy({{ $this->projetoAlvo->id }})" class="w-full">
                        {{ __('Excluir') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
</div>