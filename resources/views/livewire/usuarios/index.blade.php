<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Usuários')"
        :subtitle="__('Gerencie os usuários e acessos ao sistema')"
        :badge="$this->stats['total']"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Usuários'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('usuarios.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Novo Usuário') }}
        </flux:button>
    </x-ui.page-header>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <x-ui.stat-card
            :label="__('Total de usuários')"
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
            :label="__('Administradores')"
            :value="$this->stats['admins']"
            icon="shield-check"
            color="violet"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Usuários')"
            :value="$this->stats['usuarios']"
            icon="user"
            color="sky"
            delay="190ms"
        />
    </section>

    {{-- Toolbar --}}
    <x-usuarios.toolbar
        :search="$search"
        :filtro-role="$filtroRole"
        :filtro-status="$filtroStatus"
    />

    {{-- Table --}}
    @php
        $temFiltros = $search !== '' || $filtroRole !== '' || $filtroStatus !== '';
    @endphp

    <x-ui.table
        :total="$usuarios->total()"
        :first-item="$usuarios->firstItem()"
        :last-item="$usuarios->lastItem()"
        loading-targets="search,filtroRole,filtroStatus,sortBy,perPage,destroy"
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
                <flux:button wire:click="ativarSelecionados" size="sm" variant="subtle" icon="check-circle">
                    {{ __('Ativar') }}
                </flux:button>
                <flux:button wire:click="desativarSelecionados" size="sm" variant="subtle" icon="x-circle">
                    {{ __('Desativar') }}
                </flux:button>
                <flux:button wire:click="excluirSelecionados" size="sm" variant="danger" icon="trash">
                    {{ __('Excluir') }}
                </flux:button>
            </x-slot:selectedActions>
        @endif

        <x-slot:header>
            @if ($usuarios->total() > 0)
                <th scope="col" class="px-5 py-3 text-left">
                    <input
                        type="checkbox"
                        wire:click="selecionarTodosDaPagina"
                        :checked="count(array_intersect($this->selecionados, $usuarios->pluck('id')->all())) === $usuarios->count()"
                        :aria-label="__('Selecionar todos')"
                        class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
                    />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="name" :label="__('Usuário')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="role" :label="__('Perfil')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    {{ __('Verificação') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="created_at" :label="__('Criado em')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-4 py-3 text-left">
                    <x-ui.sortable-header field="ativo" :label="__('Status')" :sort-field="$sortField" :sort-direction="$sortDirection" />
                </th>
                <th scope="col" class="px-5 py-3 text-right">{{ __('Ações') }}</th>
            @endif
        </x-slot:header>

        @forelse ($usuarios as $usuario)
            <x-usuarios.row :usuario="$usuario" />
        @empty
            <tr wire:key="usuario-empty">
                <td colspan="7">
                    <x-ui.empty
                        icon="user-group"
                        :title="__('Nenhum usuário encontrado')"
                        :description="__('Tente ajustar sua busca ou os filtros para encontrar o que procura.')"
                    >
                        @if ($temFiltros)
                            <flux:button wire:click="clearFilters" variant="subtle" size="sm" icon="arrow-path">
                                {{ __('Limpar filtros') }}
                            </flux:button>
                        @else
                            <flux:button href="{{ route('usuarios.create') }}" wire:navigate variant="primary" size="sm" icon="plus">
                                {{ __('Cadastrar primeiro usuário') }}
                            </flux:button>
                        @endif
                    </x-ui.empty>
                </td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-ui.pagination :paginator="$usuarios" show-per-page :per-page="$perPage" />
        </x-slot:footer>
    </x-ui.table>

    {{-- Delete confirmation modal --}}
    <x-usuarios.delete-modal
        :usuario-para-excluir="$usuarioParaExcluir"
        :usuario-alvo="$this->usuarioAlvo"
    />
</div>