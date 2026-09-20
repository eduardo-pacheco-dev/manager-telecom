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

    $anexoIcon = function (?string $mime): string {
        if ($mime !== null && str_starts_with($mime, 'image/')) {
            return 'photo';
        }
        if ($mime === 'application/pdf') {
            return 'document-text';
        }

        return 'paper-clip';
    };

    $anexoColor = function (?string $mime): string {
        if ($mime !== null && str_starts_with($mime, 'image/')) {
            return 'bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400';
        }
        if ($mime === 'application/pdf') {
            return 'bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400';
        }

        return 'bg-zinc-700/10 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';
    };

    $navSections = [
        ['id' => 'identificacao', 'label' => __('Identificação'), 'icon' => 'identification'],
        ['id' => 'cronograma', 'label' => __('Cronograma'), 'icon' => 'calendar-days'],
        ['id' => 'projeto', 'label' => __('Projeto'), 'icon' => 'folder'],
        ['id' => 'descricao', 'label' => __('Descrição'), 'icon' => 'document-text'],
        ['id' => 'anexos', 'label' => __('Anexos'), 'icon' => 'paper-clip'],
        ['id' => 'comentarios', 'label' => __('Comentários'), 'icon' => 'chat-bubble-left-right'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6 scroll-smooth">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('ordens-servico.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para ordens de serviço')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('ordens-servico.index')" wire:navigate>{{ __('Ordens de Serviço') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->ordemServico->codigo }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('ordens-servico.edit', $this->ordemServico) }}" wire:navigate variant="primary" icon="pencil">
                {{ __('Editar') }}
            </flux:button>
            <flux:button
                wire:click="confirmDelete"
                variant="danger"
                icon="trash"
                :aria-label="__('Excluir')"
                :title="__('Excluir')"
            />
        </div>
    </div>

    {{-- Hero --}}
    <section class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-sky-50/70 p-6 sm:p-8 dark:border-white/10 dark:from-white/[0.06] dark:via-white/[0.03] dark:to-sky-400/[0.05]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-400/15"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-violet-600 text-lg font-bold tracking-wide text-white shadow-lg shadow-sky-500/30 ring-4 ring-sky-500/10">
                    {{ \Illuminate\Support\Str::limit($this->ordemServico->codigo, 5, '') }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->ordemServico->codigo }}</h2>
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
                        @if ($this->ordemServico->status)
                            <flux:badge :color="$statusBadgeColor" rounded>{{ $this->ordemServico->status }}</flux:badge>
                        @endif
                        @if ($this->ordemServico->prioridade)
                            <flux:badge :color="$prioridadeBadgeColor" rounded>{{ $this->ordemServico->prioridade }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        @if ($this->ordemServico->radioLink)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.radio class="size-4 text-sky-500 dark:text-sky-400" />
                                <a href="{{ route('radio-links.show', $this->ordemServico->radioLink) }}" wire:navigate class="font-medium text-sky-600 hover:underline dark:text-sky-400">
                                    {{ $this->ordemServico->radioLink->codigo }}
                                </a>
                            </span>
                        @endif
                        @if ($this->ordemServico->tipo)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.wrench-screwdriver class="size-4 text-emerald-500 dark:text-emerald-400" />
                                {{ $this->ordemServico->tipo }}
                            </span>
                        @endif
                        @if ($this->ordemServico->solicitante)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span>{{ __('Solicitante') }}: {{ $this->ordemServico->solicitante }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-4 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Abertura') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->ordemServico->data_abertura) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Agendamento') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->ordemServico->data_agendamento) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Conclusão') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->ordemServico->data_conclusao) }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Responsável') }}</p>
                    <p class="mt-1.5 truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->ordemServico->responsavel?->name ?: '—' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick nav (scrollspy) --}}
    <nav
        x-data="{ active: '{{ $navSections[0]['id'] }}' }"
        @scroll.window.passive="
            const offset = 160;
            let current = '{{ $navSections[0]['id'] }}';
            document.querySelectorAll('[data-section]').forEach((el) => {
                if (el.getBoundingClientRect().top <= offset) current = el.id;
            });
            active = current;
        "
        class="animate-fade-in-up sticky top-4 z-20 flex gap-1.5 overflow-x-auto rounded-2xl border border-zinc-200 bg-white/90 p-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90"
        style="animation-delay: 80ms"
        aria-label="{{ __('Navegação rápida') }}"
    >
        @foreach ($navSections as $section)
            <a
                href="#{{ $section['id'] }}"
                @click="active = '{{ $section['id'] }}'"
                :class="active === '{{ $section['id'] }}'
                    ? 'bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-900'
                    : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white'"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
            >
                <flux:icon :icon="$section['icon']" variant="micro" class="size-4" />
                {{ $section['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Identificação --}}
    <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon.identification class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Identificação') }}</h3>
        </header>

        <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
            @foreach ([
                ['label' => __('Título'), 'value' => $this->ordemServico->titulo],
                ['label' => __('Tipo'), 'value' => $this->ordemServico->tipo],
                ['label' => __('Solicitante'), 'value' => $this->ordemServico->solicitante],
                ['label' => __('Responsável'), 'value' => $this->ordemServico->responsavel?->name],
                ['label' => __('Status'), 'badge' => true, 'color' => $statusBadgeColor, 'value' => $this->ordemServico->status],
                ['label' => __('Prioridade'), 'badge' => true, 'color' => $prioridadeBadgeColor, 'value' => $this->ordemServico->prioridade],
            ] as $item)
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">
                        @if (isset($item['badge']) && $item['value'])
                            <flux:badge :color="$item['color']" rounded>{{ $item['value'] }}</flux:badge>
                        @else
                            {{ $item['value'] ?? '—' }}
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Vínculo --}}
    <section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 160ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                <flux:icon.radio class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Vínculo') }}</h3>
        </header>

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
    </section>

    {{-- Cronograma --}}
    <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 200ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                <flux:icon.calendar-days class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Cronograma') }}</h3>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach ([
                ['label' => __('Abertura'), 'value' => $fmtDate($this->ordemServico->data_abertura)],
                ['label' => __('Agendamento'), 'value' => $fmtDate($this->ordemServico->data_agendamento)],
                ['label' => __('Conclusão'), 'value' => $fmtDate($this->ordemServico->data_conclusao)],
            ] as $item)
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                    <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Detalhes do projeto --}}
    <section id="projeto" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 240ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-400">
                <flux:icon.folder class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Detalhes do projeto') }}</h3>
        </header>

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
    </section>

    {{-- Descrição --}}
    <section id="descricao" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-zinc-700/10 text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                <flux:icon.document-text class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Descrição') }}</h3>
        </header>

        @if ($this->ordemServico->descricao)
            <p class="whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->ordemServico->descricao }}</p>
        @else
            <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma descrição registrada.') }}</p>
        @endif
    </section>

    {{-- Anexos --}}
    <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 320ms">
        <header class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.paper-clip class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Anexos') }}</h3>
            </div>
            @if ($anexos->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    {{ $anexos->count() }} {{ __('arquivo(s)') }}
                </span>
            @endif
        </header>

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

        <div class="mt-6">
            @forelse ($anexos as $anexo)
                <div wire:key="anexo-{{ $anexo->id }}" class="flex items-center gap-3 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                    <div class="{{ $anexoColor($anexo->mime) }} flex size-10 shrink-0 items-center justify-center rounded-xl">
                        <flux:icon :icon="$anexoIcon($anexo->mime)" class="size-5" />
                    </div>
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
    </section>

    {{-- Comentários --}}
    <section id="comentarios" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 320ms">
        <header class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.chat-bubble-left-right class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Comentários') }}</h3>
            </div>
            @if ($comentarios->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                    {{ $comentarios->count() }} {{ __('comentário(s)') }}
                </span>
            @endif
        </header>

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

        <div class="mt-6">
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
    </section>
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