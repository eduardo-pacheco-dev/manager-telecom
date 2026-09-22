<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="$this->projeto->codigo"
        :subtitle="$this->projeto->nome"
        :badge="$this->projeto->status"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM Implantação RF'), 'href' => route('tim.index')],
            ['label' => $this->projeto->codigo, 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
        <flux:button href="{{ route('tim.edit', $this->projeto) }}" wire:navigate variant="primary" icon="pencil">
            {{ __('Editar') }}
        </flux:button>
        <flux:button
            wire:click="confirmDelete"
            variant="danger"
            icon="trash"
            :aria-label="__('Excluir')"
            :title="__('Excluir')"
        />
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <div class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Informações básicas do projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ match ($this->projeto->status) {
                                        'Em andamento' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20',
                                        'Pausado' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20',
                                        'Concluído' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20',
                                        'Cancelado' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20',
                                        default => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20',
                                    } }}">
                                    <span class="size-1.5 shrink-0 rounded-full
                                        {{ match ($this->projeto->status) {
                                            'Em andamento' => 'bg-sky-500 dark:bg-sky-400',
                                            'Pausado' => 'bg-amber-500 dark:bg-amber-400',
                                            'Concluído' => 'bg-emerald-500 dark:bg-emerald-400',
                                            'Cancelado' => 'bg-rose-500 dark:bg-rose-400',
                                            default => 'bg-zinc-400 dark:bg-zinc-500',
                                        } }}"></span>
                                    {{ $this->projeto->status }}
                                </span>
                                @if ($this->projeto->ativo)
                                    <flux:badge color="emerald" rounded>{{ __('Ativo') }}</flux:badge>
                                @else
                                    <flux:badge color="gray" rounded>{{ __('Inativo') }}</flux:badge>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Cliente') }}</flux:label>
                            @if ($this->projeto->cliente)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                        <flux:icon.user class="size-4" />
                                    </span>
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->projeto->cliente->nome }}</span>
                                </div>
                            @else
                                <p class="text-sm text-zinc-400 dark:text-zinc-500">—</p>
                            @endif
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Descrição') }}</flux:label>
                            @if ($this->projeto->descricao)
                                <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->projeto->descricao }}</p>
                            @else
                                <p class="text-sm text-zinc-400 dark:text-zinc-500">—</p>
                            @endif
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Ordens de Serviço --}}
            <section id="ordens" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="clipboard-document-list"
                    :title="__('Ordens de serviço vinculadas')"
                    :description="__('Vincule ordens de serviço a este projeto')"
                >
                    <div class="mb-4 rounded-xl border border-dashed border-violet-200 bg-violet-50/40 p-4 dark:border-violet-400/20 dark:bg-violet-400/5">
                        <p class="mb-3 flex items-center gap-2 text-sm font-medium text-zinc-900 dark:text-white">
                            <flux:icon.link class="size-4 text-violet-500 dark:text-violet-400" />
                            {{ __('Vincular ordem de serviço') }}
                        </p>
                        <flux:input
                            wire:model.live="buscaOs"
                            :placeholder="__('Buscar OS pelo código ou título...')"
                            icon="magnifying-glass"
                        />
                        @if ($this->ordensDisponiveis->isNotEmpty())
                            <div class="mt-3 flex flex-col divide-y divide-zinc-100 rounded-xl border border-zinc-200 bg-white dark:divide-white/5 dark:border-white/10">
                                @foreach ($this->ordensDisponiveis as $ordem)
                                    <div class="flex items-center gap-3 px-4 py-2.5">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $ordem->codigo }}</p>
                                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
                                        </div>
                                        <flux:button wire:click="vincular({{ $ordem->id }})" size="sm" variant="primary" icon="plus">
                                            {{ __('Vincular') }}
                                        </flux:button>
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($this->buscaOs !== '')
                            <p class="mt-3 text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma ordem disponível para vincular.') }}</p>
                        @endif
                    </div>

                    @if ($this->ordens->isEmpty())
                        <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                            {{ __('Nenhuma ordem de serviço vinculada a este projeto.') }}
                        </p>
                    @else
                        <div class="flex flex-col">
                            @foreach ($this->ordens as $ordem)
                                <div wire:key="projeto-os-{{ $ordem->id }}" class="group flex items-center gap-4 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                        <flux:icon.clipboard-document-list class="size-4.5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="truncate text-sm font-medium text-zinc-900 transition-colors hover:text-sky-600 dark:text-white dark:hover:text-sky-400">
                                            {{ $ordem->codigo }}
                                        </a>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
                                    </div>
                                    <flux:button
                                        wire:click="desvincular({{ $ordem->id }})"
                                        wire:confirm="{{ __('Desvincular esta ordem?') }}"
                                        size="sm"
                                        variant="ghost"
                                        icon="x-mark"
                                        :aria-label="__('Desvincular')"
                                    />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Etapas --}}
            <section id="etapas" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="list-bullet"
                    :title="__('Etapas')"
                    :description="__('Acompanhe o andamento de cada etapa do projeto')"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                            {{ $this->etapas->where('status', 'Concluída')->count() }}/{{ $this->etapas->count() }}
                            {{ __('etapas concluídas') }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-3">
                        @foreach ($this->etapas as $etapa)
                            @php
                                $etapaStyles = [
                                    'Pendente' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
                                    'Em andamento' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
                                    'Concluída' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
                                ];
                                $etapaStyle = $etapaStyles[$etapa->status] ?? $etapaStyles['Pendente'];
                            @endphp
                            <div wire:key="projeto-etapa-{{ $etapa->id }}" class="flex flex-col gap-3 rounded-xl border border-zinc-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $etapa->status === 'Concluída' ? 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400' : ($etapa->status === 'Em andamento' ? 'bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400' : 'bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500') }}">
                                        @if ($etapa->status === 'Concluída')
                                            <flux:icon.check class="size-4.5" />
                                        @elseif ($etapa->status === 'Em andamento')
                                            <flux:icon.clock class="size-4.5" />
                                        @else
                                            <flux:icon.circle-stack class="size-4.5" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $etapa->etapa }}</p>
                                        @if ($etapa->data_conclusao)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Concluída em') }} {{ $etapa->data_conclusao->format('d/m/Y') }}</p>
                                        @elseif ($etapa->data_real)
                                            <p class="text-xs text-emerald-600 dark:text-emerald-400">{{ __('Real') }} {{ $etapa->data_real->format('d/m/Y') }}</p>
                                        @elseif ($etapa->data_planejada)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Planejada') }} {{ $etapa->data_planejada->format('d/m/Y') }}</p>
                                        @elseif ($etapa->data_baseline)
                                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('Baseline') }} {{ $etapa->data_baseline->format('d/m/Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $etapaStyle['badge'] }}">
                                        <span class="size-1.5 shrink-0 rounded-full {{ $etapaStyle['dot'] }}"></span>
                                        {{ $etapa->status }}
                                    </span>
                                    <div class="flex items-center gap-1">
                                        @if ($etapa->status !== 'Pendente')
                                            <flux:button
                                                wire:click="retrocederEtapa({{ $etapa->id }})"
                                                size="sm"
                                                variant="ghost"
                                                icon="arrow-left"
                                                :title="__('Retroceder')"
                                                :aria-label="__('Retroceder')"
                                            />
                                        @endif
                                        @if ($etapa->status !== 'Concluída')
                                            <flux:button
                                                wire:click="avancarEtapa({{ $etapa->id }})"
                                                size="sm"
                                                variant="ghost"
                                                icon="arrow-right"
                                                :title="__('Avançar')"
                                                :aria-label="__('Avançar')"
                                            />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Relatórios --}}
            <section id="relatorios" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Relatórios')"
                    :description="__('Relatórios vinculados ao projeto')"
                >
                    <div class="mb-4 flex items-center justify-end">
                        <flux:button href="{{ route('tim.relatorios.create', $this->projeto) }}" wire:navigate variant="primary" size="sm" icon="plus">
                            {{ __('Novo relatório') }}
                        </flux:button>
                    </div>

                    @if ($this->relatorios->isEmpty())
                        <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                            {{ __('Nenhum relatório criado para este projeto.') }}
                        </p>
                    @else
                        <div class="flex flex-col">
                            @foreach ($this->relatorios as $relatorio)
                                @php
                                    $relStatusStyles = [
                                        'Pendente' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20',
                                        'Em andamento' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20',
                                        'Concluído' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20',
                                        'Cancelado' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20',
                                    ];
                                    $relStatusStyle = $relStatusStyles[$relatorio->status] ?? 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10';
                                @endphp
                                <div wire:key="projeto-relatorio-{{ $relatorio->id }}" class="group flex items-center gap-4 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                        <flux:icon.document-text class="size-4.5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('tim.relatorios.show', [$this->projeto, $relatorio]) }}" wire:navigate class="truncate text-sm font-medium text-zinc-900 transition-colors hover:text-sky-600 dark:text-white dark:hover:text-sky-400">
                                            {{ $relatorio->ordemServico?->codigo ?: 'Relatório #'.$relatorio->id }}
                                        </a>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $relatorio->estacao?->site_id ?: '—' }}
                                            @if ($relatorio->data_planejada) · {{ __('Planejada') }}: {{ $relatorio->data_planejada->format('d/m/Y') }} @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $relStatusStyle }}">
                                        {{ $relatorio->status }}
                                    </span>
                                    <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-300 transition-transform group-hover:translate-x-0.5 dark:text-zinc-600" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Anexos --}}
            <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 160ms">
                <x-ui.form-section
                    icon="paper-clip"
                    :title="__('Anexos')"
                    :description="__('TSSR, DOC-D e notas fiscais do projeto')"
                >
                    @php
                        $anexosPorCategoria = $this->projeto->anexos->groupBy('categoria');
                    @endphp

                    @if ($this->projeto->anexos->isEmpty())
                        <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                            {{ __('Nenhum anexo cadastrado.') }}
                        </p>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach (App\Models\TimProjetoAnexo::CATEGORIAS as $categoria)
                                @php
                                    $anexosCategoria = $anexosPorCategoria->get($categoria, collect());
                                @endphp
                                <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                                    <p class="mb-2.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.folder class="size-3.5" />
                                        {{ $categoria }}
                                        <span class="ml-auto rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/5 dark:text-zinc-400">{{ $anexosCategoria->count() }}</span>
                                    </p>

                                    @if ($anexosCategoria->isEmpty())
                                        <p class="text-xs text-zinc-300 dark:text-zinc-600">—</p>
                                    @else
                                        <ul class="flex flex-col gap-1.5">
                                            @foreach ($anexosCategoria as $anexo)
                                                <li wire:key="projeto-anexo-{{ $anexo->id }}" class="group flex items-center gap-2 rounded-lg bg-zinc-50 p-2 dark:bg-white/5">
                                                    <flux:icon.document class="size-4 shrink-0 text-sky-500" />
                                                    <a
                                                        href="{{ route('tim.anexos.download', $anexo) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="min-w-0 flex-1 truncate text-xs font-medium text-zinc-700 hover:text-sky-600 dark:text-zinc-300 dark:hover:text-sky-400"
                                                        title="{{ $anexo->nome }}"
                                                    >
                                                        {{ $anexo->nome }}
                                                    </a>
                                                    <flux:button
                                                        wire:click="removerAnexo({{ $anexo->id }})"
                                                        wire:confirm="{{ __('Remover este anexo?') }}"
                                                        variant="ghost"
                                                        size="xs"
                                                        icon="trash"
                                                        :aria-label="__('Remover')"
                                                        :title="__('Remover')"
                                                    />
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.form-section>
            </section>
        </div>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['ordens', 'clipboard-document-list', __('Ordens de Serviço')],
            ['etapas', 'list-bullet', __('Etapas')],
            ['relatorios', 'document-text', __('Relatórios')],
            ['anexos', 'paper-clip', __('Anexos')],
        ]">
            <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4 dark:border-amber-400/20 dark:bg-amber-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-amber-700 dark:text-amber-300">
                    <flux:icon.clock class="size-4" />
                    {{ __('Histórico') }}
                </p>

                @if ($this->historicos->isEmpty())
                    <p class="mt-2 text-xs text-amber-800/70 dark:text-amber-200/70">
                        {{ __('Nenhuma atividade registrada.') }}
                    </p>
                @else
                    <ol class="relative ml-2 mt-3 flex flex-col border-l border-amber-200 dark:border-amber-400/20">
                        @foreach ($this->historicos as $historico)
                            @php
                                $tipoStyles = [
                                    'criacao' => ['dot' => 'bg-violet-500', 'iconColor' => 'text-violet-500 dark:text-violet-400'],
                                    'os_vinculada' => ['dot' => 'bg-sky-500', 'iconColor' => 'text-sky-500 dark:text-sky-400'],
                                    'os_desvinculada' => ['dot' => 'bg-zinc-400', 'iconColor' => 'text-zinc-400 dark:text-zinc-500'],
                                    'etapa_alterada' => ['dot' => 'bg-amber-500', 'iconColor' => 'text-amber-500 dark:text-amber-400'],
                                    'relatorio_criado' => ['dot' => 'bg-emerald-500', 'iconColor' => 'text-emerald-500 dark:text-emerald-400'],
                                    'relatorio_excluido' => ['dot' => 'bg-rose-500', 'iconColor' => 'text-rose-500 dark:text-rose-400'],
                                ];
                                $tipoStyle = $tipoStyles[$historico->tipo] ?? ['dot' => 'bg-zinc-400', 'iconColor' => 'text-zinc-400 dark:text-zinc-500'];
                            @endphp
                            <li class="relative pb-4 pl-6 last:pb-0">
                                <span class="absolute left-[-3px] top-1 flex size-2 shrink-0 rounded-full ring-2 ring-white dark:ring-white/10 {{ $tipoStyle['dot'] }}"></span>
                                <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200">{{ $historico->descricao }}</p>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-zinc-400 dark:text-zinc-500">
                                    <span class="inline-flex items-center gap-1">
                                        <flux:icon.calendar class="size-3" />
                                        {{ $historico->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if ($historico->user)
                                        <span class="inline-flex items-center gap-1">
                                            <flux:icon.user class="size-3" />
                                            {{ $historico->user->name }}
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </x-ui.form-nav>
    </div>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir projeto') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->projeto->codigo }}</strong>? {{ __('As ordens de serviço vinculadas serão desvinculadas.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif