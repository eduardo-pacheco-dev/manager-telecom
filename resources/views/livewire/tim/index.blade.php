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
        <flux:button
            wire:click="abrirImportacao"
            variant="filled"
            icon="arrow-up-tray"
        >
            {{ __('Importar') }}
        </flux:button>
        <flux:button href="{{ route('tim.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Novo Projeto') }}
        </flux:button>
    </x-ui.page-header>

    {{-- Importações: notifica via toast --}}
    <div wire:poll.5s="verificarImportacoes" class="hidden" aria-hidden="true"></div>

    {{-- Stats --}}
    @php
        $total = (int) $this->stats['total'];
        $ativos = (int) $this->stats['ativos'];
        $concluidos = (int) $this->stats['concluidos'];

        $pctAtivos = $total > 0 ? (int) round(($ativos / $total) * 100) : 0;
        $pctConcluidos = $total > 0 ? (int) round(($concluidos / $total) * 100) : 0;
    @endphp

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="{{ __('Resumo') }}">
        <x-ui.stat-card
            :label="__('Total de projetos')"
            :value="$total"
            icon="folder"
            color="zinc"
            :progress="100"
            :footnote="__('Inventário completo')"
        />

        <x-ui.stat-card
            :label="__('Ativos')"
            :value="$ativos"
            icon="check-circle"
            color="emerald"
            :progress="$pctAtivos"
            :footnote="$pctAtivos.'% '.__('do total')"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Concluídos')"
            :value="$concluidos"
            icon="flag"
            color="violet"
            :progress="$pctConcluidos"
            :footnote="$pctConcluidos.'% '.__('do total')"
            delay="140ms"
        />
    </section>

    {{-- Toolbar --}}
    <x-tim.toolbar
        :search="$search"
        :filtro-status="$filtroStatus"
    />

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
            <x-tim.row :projeto="$projeto" />
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

    {{-- Modal de importação --}}
    <x-tim.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />

    {{-- Delete confirmation modal --}}
    <x-tim.delete-modal
        :projeto-para-excluir="$projetoParaExcluir"
        :projeto-alvo="$this->projetoAlvo"
    />
</div>