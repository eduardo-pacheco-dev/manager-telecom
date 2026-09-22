@php
    $statusBadgeColors = [
        'Aberta' => 'sky',
        'Em andamento' => 'amber',
        'Aguardando' => 'gray',
        'Concluída' => 'emerald',
        'Cancelada' => 'red',
    ];
    $statusBadgeColor = $statusBadgeColors[$this->ordemServico->status] ?? 'gray';

    $prioridadeBadgeColors = [
        'Baixa' => 'gray',
        'Média' => 'sky',
        'Alta' => 'amber',
        'Urgente' => 'red',
    ];
    $prioridadeBadgeColor = $prioridadeBadgeColors[$this->ordemServico->prioridade] ?? 'gray';

    $fmtDate = fn ($value): string => $value?->format('d/m/Y') ?? '—';

    $fmtBytes = function (?int $bytes): string {
        if ($bytes === null) {
            return '—';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.').' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return $bytes.' B';
    };
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="$this->ordemServico->codigo"
        :subtitle="$this->ordemServico->titulo"
        :badge="$this->ordemServico->status"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Ordens de Serviço'), 'href' => route('ordens-servico.index')],
            ['label' => $this->ordemServico->codigo, 'href' => null],
        ]"
    >
        <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
        <flux:button href="{{ route('ordens-servico.edit', $this->ordemServico) }}" wire:navigate variant="primary" icon="pencil">
            {{ __('Editar') }}
        </flux:button>
        <flux:button
            wire:click="confirmDelete"
            variant="danger"
            icon="trash"
        >
            {{ __('Excluir') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <div class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Dados básicos da ordem de serviço')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($this->ordemServico->status)
                                    <flux:badge :color="$statusBadgeColor" rounded>{{ $this->ordemServico->status }}</flux:badge>
                                @endif
                                @if ($this->ordemServico->prioridade)
                                    <flux:badge :color="$prioridadeBadgeColor" rounded>{{ $this->ordemServico->prioridade }}</flux:badge>
                                @endif
                                @if ($this->ordemServico->escopo)
                                    <flux:badge rounded :color="match ($this->ordemServico->escopo) {
                                        'Enlace' => 'sky',
                                        'Estação' => 'violet',
                                        default => 'gray',
                                    }">
                                        <flux:icon.radio variant="micro" class="me-1" />
                                        {{ $this->ordemServico->escopo }}
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Cliente') }}</flux:label>
                            @if ($this->ordemServico->cliente)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                        <flux:icon.building-office class="size-4" />
                                    </span>
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->ordemServico->cliente->nome }}</span>
                                </div>
                            @else
                                <p class="text-sm text-zinc-400 dark:text-zinc-500">—</p>
                            @endif
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                        @foreach ([
                            ['label' => __('Tipo'), 'value' => $this->ordemServico->tipo],
                            ['label' => __('Código do cliente'), 'value' => $this->ordemServico->codigo_cliente],
                            ['label' => __('Ordem complexa'), 'value' => $this->ordemServico->ordem_complexa],
                            ['label' => __('Solicitante'), 'value' => $this->ordemServico->solicitante],
                            ['label' => __('Responsável'), 'value' => $this->ordemServico->responsavel?->name],
                        ] as $item)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</dt>
                                <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $item['value'] ?? '—' }}</dd>
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Vínculo --}}
            <section id="vinculo" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="radio"
                    :title="__('Vínculo')"
                    :description="__('Enlace ou estação associada à ordem')"
                >
                    @if ($this->ordemServico->escopo === 'Enlace' && $this->ordemServico->radioLink)
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex min-w-0 items-center gap-3.5">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                    <flux:icon.radio class="size-5" />
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('radio-links.show', $this->ordemServico->radioLink) }}" wire:navigate class="text-sm font-semibold text-sky-600 transition-colors hover:underline dark:text-sky-400">
                                        {{ $this->ordemServico->radioLink->codigo }}
                                    </a>
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                                        <span>{{ $this->ordemServico->estacaoA?->site_id ?? '—' }}</span>
                                        <flux:icon.arrow-right class="size-3.5 text-zinc-300 dark:text-zinc-600" />
                                        <span>{{ $this->ordemServico->estacaoB?->site_id ?? '—' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($this->ordemServico->escopo === 'Estação' && $this->ordemServico->estacaoA)
                        <div class="flex min-w-0 items-center gap-3.5">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                <flux:icon.signal class="size-5" />
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('estacoes.show', $this->ordemServico->estacaoA) }}" wire:navigate class="text-sm font-semibold text-sky-600 transition-colors hover:underline dark:text-sky-400">
                                    {{ $this->ordemServico->estacaoA->site_id }}
                                </a>
                                <p class="mt-1 flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                                    <flux:icon.map-pin class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                                    <span>{{ $this->ordemServico->estacaoA->municipio ?: __('Sem município') }}@if ($this->ordemServico->estacaoA->estado) · {{ $this->ordemServico->estacaoA->estado }}@endif</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Ordem sem vínculo com enlace ou estação.') }}</p>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Datas da execução')"
                >
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                            ['label' => __('Abertura'), 'value' => $fmtDate($this->ordemServico->data_abertura)],
                            ['label' => __('Conclusão'), 'value' => $fmtDate($this->ordemServico->data_conclusao)],
                        ] as $item)
                            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                                <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Detalhes do projeto --}}
            <section id="projeto" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="folder"
                    :title="__('Detalhes do projeto')"
                    :description="__('Informações complementares da ordem')"
                >
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['label' => __('Projeto'), 'value' => $this->ordemServico->projeto],
                            ['label' => __('Supervisor'), 'value' => $this->ordemServico->supervisor],
                            ['label' => __('Coordenador'), 'value' => $this->ordemServico->coordenador],
                            ['label' => __('OC (TIM)'), 'value' => $this->ordemServico->oc_tim],
                            ['label' => __('Chave MW'), 'value' => $this->ordemServico->chave_mw],
                            ['label' => __('SMP Nokia'), 'value' => $this->ordemServico->smp_nokia],
                            ['label' => __('END ID A'), 'value' => $this->ordemServico->end_id_a],
                            ['label' => __('END ID B'), 'value' => $this->ordemServico->end_id_b],
                        ] as $item)
                            <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                                <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                                <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] ?? '—' }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if ($this->ordemServico->observacao)
                        <div class="mt-4 rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                            <p class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                <flux:icon.chat-bubble-left-ellipsis variant="micro" class="size-3.5" />
                                {{ __('Observação geral') }}
                            </p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->ordemServico->observacao }}</p>
                        </div>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Descrição --}}
            <section id="descricao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 160ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Descrição')"
                    :description="__('Detalhes e instruções do serviço')"
                >
                    @if ($this->ordemServico->descricao)
                        <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->ordemServico->descricao }}</p>
                    @else
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma descrição registrada.') }}</p>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Anexos --}}
            <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 200ms">
                <x-ui.form-section
                    icon="paper-clip"
                    :title="__('Anexos')"
                    :description="__('Laudos, fotos ou documentos da ordem')"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <flux:input type="file" wire:model="anexo_arquivo" />
                            <flux:error name="anexo_arquivo" />
                        </div>
                        <flux:button
                            wire:click="saveAnexo"
                            variant="primary"
                            icon="arrow-up-tray"
                            wire:loading.attr="disabled"
                            wire:target="saveAnexo"
                        >
                            {{ __('Anexar arquivo') }}
                        </flux:button>
                    </div>

                    <div class="mt-4">
                        @forelse ($anexos as $anexo)
                            <div wire:key="anexo-{{ $anexo->id }}" class="flex items-center gap-3 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                                <x-ui.file-icon :mime="$anexo->mime" container="size-10" icon="size-5" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $anexo->nome }}</p>
                                    <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                                        {{ $fmtBytes($anexo->tamanho) }} · {{ $anexo->created_at?->format('d/m/Y') }}
                                    </p>
                                </div>
                                <a
                                    href="{{ route('ordens-servico.anexos.download', $anexo) }}"
                                    class="inline-flex items-center rounded-lg px-2 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                                    aria-label="{{ __('Baixar') }}"
                                >
                                    <flux:icon.arrow-down-tray class="size-4" />
                                </a>
                                <flux:button
                                    wire:click="removerAnexo({{ $anexo->id }})"
                                    wire:confirm="{{ __('Remover este anexo?') }}"
                                    variant="ghost"
                                    icon="trash"
                                    size="sm"
                                    :aria-label="__('Remover anexo')"
                                />
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                {{ __('Nenhum anexo registrado. Adicione laudos, fotos ou documentos da ordem.') }}
                            </p>
                        @endforelse
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Comentários --}}
            <section id="comentarios" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 240ms">
                <x-ui.form-section
                    icon="chat-bubble-left-right"
                    :title="__('Comentários')"
                    :description="__('Histórico de conversas da ordem')"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <flux:textarea wire:model="comentario" rows="3" placeholder="{{ __('Escreva um comentário sobre a ordem...') }}" />
                            <flux:error name="comentario" />
                        </div>
                        <flux:button
                            wire:click="addComentario"
                            variant="primary"
                            icon="paper-airplane"
                            wire:loading.attr="disabled"
                            wire:target="addComentario"
                        >
                            {{ __('Comentar') }}
                        </flux:button>
                    </div>

                    <div class="mt-4">
                        @forelse ($comentarios as $comentario)
                            <div wire:key="comentario-{{ $comentario->id }}" class="flex gap-3 border-t border-zinc-100 py-4 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-500/10 text-xs font-bold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                    {{ \Illuminate\Support\Str::initials($comentario->user->name, true) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $comentario->user->name }}</p>
                                            <span class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if ($comentario->user_id === auth()->id())
                                            <flux:button
                                                wire:click="removerComentario({{ $comentario->id }})"
                                                wire:confirm="{{ __('Remover este comentário?') }}"
                                                variant="ghost"
                                                icon="trash"
                                                size="sm"
                                                :aria-label="__('Remover comentário')"
                                            />
                                        @endif
                                    </div>
                                    <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $comentario->conteudo }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                {{ __('Nenhum comentário ainda. Seja a primeira pessoa a comentar.') }}
                            </p>
                        @endforelse
                    </div>
                </x-ui.form-section>
            </section>
        </div>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['vinculo', 'radio', __('Vínculo')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['projeto', 'folder', __('Projeto')],
            ['descricao', 'document-text', __('Descrição')],
            ['anexos', 'paper-clip', __('Anexos')],
            ['comentarios', 'chat-bubble-left-right', __('Comentários')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Acompanhe a ordem pelo status e prioridade. Use os comentários para registrar o andamento.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir ordem de serviço') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->ordemServico->codigo }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif