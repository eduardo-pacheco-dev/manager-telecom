<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Estações')"
        :subtitle="__('Gerencie as estações de telecomunicações')"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Estações'), 'href' => null],
        ]"
    >
        <x-slot:titleBadge>
            <flux:button
                href="{{ route('estacoes.configuracoes') }}"
                wire:navigate
                variant="ghost"
                size="sm"
                icon="cog-6-tooth"
                square
                :title="__('Configurações')"
                :aria-label="__('Configurações')"
            />
        </x-slot:titleBadge>
        <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Nova Estação') }}
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
            :label="__('Total de estações')"
            :value="$this->stats['total']"
            icon="signal"
            color="zinc"
        />

        <x-ui.stat-card
            :label="__('Tecnologias')"
            :value="$this->stats['tecnologias']"
            icon="cpu-chip"
            color="emerald"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Municípios')"
            :value="$this->stats['municipios']"
            icon="map-pin"
            color="sky"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Tipos de elemento')"
            :value="$this->stats['elementos']"
            icon="radio"
            color="violet"
            delay="190ms"
        />
    </section>

    {{-- Toolbar --}}
    <x-estacoes.toolbar
        :search="$search"
        :filtro-tipo-elemento="$filtroTipoElemento"
        :filtro-status="$filtroStatus"
        :tipos-elemento="$this->tiposElemento"
        :statuses="$this->statuses"
    />

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroTipoElemento !== '' || $filtroStatus !== '';
    @endphp

    <x-ui.table
        :total="$estacoes->total()"
        :first-item="$estacoes->firstItem()"
        :last-item="$estacoes->lastItem()"
        loading-targets="search,filtroTipoElemento,filtroStatus,sortBy,perPage,destroy"
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
            @if ($estacoes->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        @checked(count(array_intersect($this->selecionados, $estacoes->pluck('id')->all())) === $estacoes->count())
                        aria-label="{{ __('Selecionar todos') }}"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="site_id" :label="__('Estação')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="tipo_elemento" :label="__('Elemento / Tecnologia')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="classificacao" :label="__('Classificação')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="municipio" :label="__('Município')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-right">
                    <x-ui.sortable-header field="data_aquisicao" :label="__('Aquisição')" align="right" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($estacoes as $estacao)
            <x-estacoes.row :estacao="$estacao" />
        @empty
            <tr wire:key="estacao-empty">
                <td colspan="8">
                    <x-ui.empty
                        icon="signal"
                        :title="__('Nenhuma estação encontrada')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeira estação') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$estacoes" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Delete confirmation modal --}}
    <x-estacoes.delete-modal
        :estacao-para-excluir="$estacaoParaExcluir"
        :estacao-alvo="$this->estacaoAlvo"
    />

    {{-- Import modal --}}
    <x-estacoes.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />
</div>