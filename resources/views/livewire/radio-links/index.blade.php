<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Radio Links')"
        :subtitle="__('Gerencie os enlaces de rádio entre estações')"
        :badge="$this->stats['total']"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Radio Links'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('radio-links.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Novo Radio Link') }}
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
            :label="__('Total de radio links')"
            :value="$this->stats['total']"
            icon="radio"
            color="zinc"
        />

        <x-ui.stat-card
            :label="__('Links ativos')"
            :value="$this->stats['ativos']"
            icon="bolt"
            color="emerald"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Fabricantes')"
            :value="$this->stats['fabricantes']"
            icon="cpu-chip"
            color="sky"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Estações conectadas')"
            :value="$this->stats['estacoes']"
            icon="signal"
            color="violet"
            delay="190ms"
        />
    </section>

    {{-- Toolbar --}}
    <x-radio-links.toolbar
        :search="$search"
        :filtro-status="$filtroStatus"
        :filtro-fabricante="$filtroFabricante"
        :statuses="$this->statuses"
        :fabricantes="$this->fabricantes"
    />

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroStatus !== '' || $filtroFabricante !== '';
    @endphp

    <x-ui.table
        :total="$radioLinks->total()"
        :first-item="$radioLinks->firstItem()"
        :last-item="$radioLinks->lastItem()"
        loading-targets="search,filtroStatus,filtroFabricante,sortBy,perPage,destroy"
        :has-filters="$temFiltros"
        :min-width="'72rem'"
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
            @if ($radioLinks->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        @checked(count(array_intersect($this->selecionados, $radioLinks->pluck('id')->all())) === $radioLinks->count())
                        aria-label="{{ __('Selecionar todos') }}"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="codigo" :label="__('Radio Link')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="estacao_a_id" :label="__('Estações')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="frequencia" :label="__('Frequência')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="capacidade" :label="__('Capacidade')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    {{ __('Fabricante') }}
                </th>
                <th scope="col" class="px-4 py-3 text-right">
                    <x-ui.sortable-header field="data_ativacao" :label="__('Ativação')" align="right" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($radioLinks as $radioLink)
            <x-radio-links.row :radio-link="$radioLink" />
        @empty
            <tr wire:key="radio-link-empty">
                <td colspan="9">
                    <x-ui.empty
                        icon="radio"
                        :title="__('Nenhum radio link encontrado')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('radio-links.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeiro radio link') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$radioLinks" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Delete confirmation modal --}}
    <x-radio-links.delete-modal
        :radio-link-para-excluir="$radioLinkParaExcluir"
        :radio-link-alvo="$this->radioLinkAlvo"
    />

    {{-- Import modal --}}
    <x-radio-links.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />
</div>