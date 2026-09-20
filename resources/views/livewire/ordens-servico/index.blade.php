<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Ordens de Serviço')"
        :subtitle="__('Gerencie as ordens de serviço dos enlaces')"
        :badge="$this->stats['total']"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Ordens de Serviço'), 'href' => null],
        ]"
    >
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
    </x-ui.page-header>

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
    <x-ordens-servico.toolbar
        :search="$search"
        :filtro-status="$filtroStatus"
        :filtro-tipo="$filtroTipo"
        :filtro-prioridade="$filtroPrioridade"
        :statuses="$this->statuses"
        :tipos="$this->tipos"
    />

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroStatus !== '' || $filtroTipo !== '' || $filtroPrioridade !== '';
    @endphp

    <x-ui.table
        :total="$ordensServico->total()"
        :first-item="$ordensServico->firstItem()"
        :last-item="$ordensServico->lastItem()"
        loading-targets="search,filtroStatus,filtroTipo,filtroPrioridade,sortBy,perPage,destroy"
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
            @if ($ordensServico->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        :checked="count(array_intersect($this->selecionados, $ordensServico->pluck('id')->all())) === $ordensServico->count()"
                        :aria-label="__('Selecionar todos')"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="codigo" :label="__('Ordem')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="titulo" :label="__('Título')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="tipo" :label="__('Tipo')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="prioridade" :label="__('Prioridade')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-right">
                    <x-ui.sortable-header field="data_abertura" :label="__('Abertura')" align="right" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="status" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($ordensServico as $ordem)
            <x-ordens-servico.row :ordem="$ordem" />
        @empty
            <tr wire:key="os-empty">
                <td colspan="8">
                    <x-ui.empty
                        icon="clipboard-document-list"
                        :title="__('Nenhuma ordem de serviço encontrada')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('ordens-servico.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeira ordem') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$ordensServico" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Modal de importação --}}
    <x-ordens-servico.import-modal
        :show-import-modal="$showImportModal"
        :import-arquivo="$import_arquivo"
    />

    {{-- Delete confirmation modal --}}
    <x-ordens-servico.delete-modal
        :ordem-para-excluir="$ordemParaExcluir"
        :ordem-alvo="$this->ordemAlvo"
    />
</div>