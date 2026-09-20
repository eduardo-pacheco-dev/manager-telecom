<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Clientes')"
        :subtitle="__('Gerencie os clientes da empresa')"
        :badge="$this->stats['total']"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Clientes'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('clientes.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Novo Cliente') }}
        </flux:button>
        <flux:button
            wire:click="abrirImportacao"
            variant="filled"
            icon="arrow-up-tray"
        >
            {{ __('Importar') }}
        </flux:button>
    </x-ui.page-header>

    {{-- Importações: notifica via toast --}}
    <div wire:poll.5s="verificarImportacoes" class="hidden" aria-hidden="true"></div>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <x-ui.stat-card
            :label="__('Total de clientes')"
            :value="$this->stats['total']"
            icon="user-group"
            color="zinc"
        />

        <x-ui.stat-card
            :label="__('Ativos')"
            :value="$this->stats['ativos']"
            icon="check-circle"
            color="emerald"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Inativos')"
            :value="$this->stats['inativos']"
            icon="x-circle"
            color="rose"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Segmentos')"
            :value="$this->stats['segmentos']"
            icon="building-office-2"
            color="sky"
            delay="190ms"
        />
    </section>

    {{-- Toolbar --}}
    <x-clientes.toolbar
        :search="$search"
        :filtro-segmento="$filtroSegmento"
        :filtro-status="$filtroStatus"
        :segmentos="$this->segmentos"
    />

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroSegmento !== '' || $filtroStatus !== '';
    @endphp

    <x-ui.table
        :total="$clientes->total()"
        :first-item="$clientes->firstItem()"
        :last-item="$clientes->lastItem()"
        loading-targets="search,filtroSegmento,filtroStatus,sortBy,perPage,toggleAtivo,destroy"
        :has-filters="$temFiltros"
        :min-width="'56rem'"
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
                <flux:button wire:click="ativarSelecionados" size="sm" variant="subtle" icon="check">
                    {{ __('Ativar') }}
                </flux:button>
                <flux:button wire:click="desativarSelecionados" size="sm" variant="subtle" icon="x-mark">
                    {{ __('Desativar') }}
                </flux:button>
                <flux:button wire:click="excluirSelecionados" size="sm" variant="danger" icon="trash">
                    {{ __('Excluir') }}
                </flux:button>
            </x-slot:selectedActions>
        @endif

        <x-slot:header>
            @if ($clientes->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        :checked="count(array_intersect($this->selecionados, $clientes->pluck('id')->all())) === $clientes->count()"
                        :aria-label="__('Selecionar todos')"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="nome" :label="__('Cliente')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="documento" :label="__('CNPJ')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="segmento" :label="__('Segmento')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="cidade" :label="__('Cidade')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="ativo" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($clientes as $cliente)
            <x-clientes.row :cliente="$cliente" />
        @empty
            <tr wire:key="cliente-empty">
                <td colspan="7">
                    <x-ui.empty
                        icon="user-group"
                        :title="__('Nenhum cliente encontrado')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('clientes.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeiro cliente') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$clientes" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Delete confirmation modal --}}
    <x-clientes.delete-modal
        :cliente-para-excluir="$clienteParaExcluir"
        :cliente-alvo="$this->clienteAlvo"
    />

    {{-- Import modal --}}
    <x-clientes.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />
</div>