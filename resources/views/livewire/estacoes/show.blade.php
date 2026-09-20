@php
    $statusBadgeColors = [
        'CANDIDATO A' => 'amber',
        'Aquisitado' => 'sky',
        'Adquirido' => 'sky',
        'Em construção' => 'amber',
        'Ativo' => 'emerald',
        'Inativo' => 'gray',
        'Desativado' => 'red',
        'Cancelado' => 'red',
    ];
    $statusBadgeColor = $statusBadgeColors[$this->estacao->status] ?? 'gray';

    $classificacaoBadgeColors = [
        'ACESSO' => 'sky',
        'RANSHARING' => 'violet',
        'BACKHAUL' => 'emerald',
        'TRANSPORTE' => 'amber',
    ];
    $classificacaoBadgeColor = $classificacaoBadgeColors[$this->estacao->classificacao] ?? 'gray';

    $fmtNumber = fn ($value, int $decimals = 2): string => $value !== null
        ? number_format((float) $value, $decimals, ',', '.')
        : '—';
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

    $enderecoCompleto = trim(($this->estacao->tipo_logradouro ?? '').' '.($this->estacao->logradouro ?? ''));
    if ($enderecoCompleto !== '' && $this->estacao->numero) {
        $enderecoCompleto .= ', '.$this->estacao->numero;
    }
    $enderecoCompleto = $enderecoCompleto ?: '—';

    $cidadeUf = trim(($this->estacao->municipio ?? '').($this->estacao->estado ? ' - '.$this->estacao->estado : '')) ?: '—';

    $lifecycle = [
        ['label' => __('Aquisição'), 'date' => $this->estacao->data_aquisicao, 'icon' => 'document-arrow-down'],
        ['label' => __('Construção'), 'date' => $this->estacao->data_construcao, 'icon' => 'wrench-screwdriver'],
        ['label' => __('Ativação'), 'date' => $this->estacao->data_ativacao, 'icon' => 'bolt'],
        ['label' => __('Desativação'), 'date' => $this->estacao->data_desativacao, 'icon' => 'power'],
        ['label' => __('Cancelamento'), 'date' => $this->estacao->data_cancelamento, 'icon' => 'x-circle'],
    ];
    $currentStep = null;
    foreach ($lifecycle as $index => $step) {
        if ($step['date'] !== null) {
            $currentStep = $index;
        }
    }
    $completedSteps = $currentStep === null ? 0 : $currentStep + 1;
    $progress = (int) round(($completedSteps / count($lifecycle)) * 100);

    $navSections = [
        ['id' => 'identificacao', 'label' => __('Identificação'), 'icon' => 'identification'],
        ['id' => 'vida', 'label' => __('Ciclo de vida'), 'icon' => 'calendar-days'],
        ['id' => 'endereco', 'label' => __('Endereço'), 'icon' => 'map-pin'],
        ['id' => 'estrutura', 'label' => __('Estrutura'), 'icon' => 'server-stack'],
        ['id' => 'contratos', 'label' => __('Contratos'), 'icon' => 'clipboard-document-list'],
        ['id' => 'anotacoes', 'label' => __('Anotações'), 'icon' => 'document-text'],
        ['id' => 'anexos', 'label' => __('Anexos'), 'icon' => 'paper-clip'],
        ['id' => 'comentarios', 'label' => __('Comentários'), 'icon' => 'chat-bubble-left-right'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6 scroll-smooth">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('estacoes.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para estações')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('estacoes.index')" wire:navigate>{{ __('Estações') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->estacao->site_id }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('estacoes.edit', $this->estacao) }}" wire:navigate variant="primary" icon="pencil">
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
                    {{ \Illuminate\Support\Str::limit($this->estacao->site_id, 5, '') }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->estacao->site_id }}</h2>
                        @if ($this->estacao->status)
                            <flux:badge :color="$statusBadgeColor" rounded>{{ $this->estacao->status }}</flux:badge>
                        @endif
                        @if ($this->estacao->classificacao)
                            <flux:badge :color="$classificacaoBadgeColor" rounded>{{ $this->estacao->classificacao }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        @if ($this->estacao->tipo_elemento)
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.radio class="size-4 text-sky-500 dark:text-sky-400" />
                                {{ $this->estacao->tipo_elemento }}
                            </span>
                        @endif
                        @if ($this->estacao->tecnologia)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.cpu-chip class="size-4 text-emerald-500 dark:text-emerald-400" />
                                {{ $this->estacao->tecnologia }}
                            </span>
                        @endif
                        @if ($this->estacao->municipio)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.map-pin class="size-4 text-violet-500 dark:text-violet-400" />
                                {{ $cidadeUf }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-4 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Classificação') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->estacao->classificacao ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Regional') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->estacao->regional ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de conexão') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->estacao->tipo_conexao ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Aquisição') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_aquisicao) }}</p>
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

    {{-- Content --}}
    <div class="animate-fade-in-up grid items-start gap-6 lg:grid-cols-3" style="animation-delay: 120ms">
        <div class="flex flex-col gap-6 lg:col-span-2">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                        <flux:icon.identification class="size-4.5" />
                    </div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Identificação') }}</h3>
                </header>

                <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                    @foreach ([
                        ['label' => __('Tipo de elemento'), 'value' => $this->estacao->tipo_elemento],
                        ['label' => __('Tecnologia'), 'value' => $this->estacao->tecnologia],
                        ['label' => __('Tipo de conexão'), 'value' => $this->estacao->tipo_conexao],
                        ['label' => __('Endereço ID'), 'value' => $this->estacao->endereco_id],
                        ['label' => __('Station ID'), 'value' => $this->estacao->station_id],
                        ['label' => __('Ordem Complexa'), 'value' => $this->estacao->ordem_complexa],
                        ['label' => __('Classificação'), 'badge' => true, 'color' => $classificacaoBadgeColor, 'value' => $this->estacao->classificacao],
                        ['label' => __('Status'), 'badge' => true, 'color' => $statusBadgeColor, 'value' => $this->estacao->status],
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

            {{-- Endereço --}}
            <section id="endereco" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                        <flux:icon.map-pin class="size-4.5" />
                    </div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Endereço') }}</h3>
                </header>

                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3.5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-400">
                            <flux:icon.map class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium leading-relaxed text-zinc-900 dark:text-white">{{ $enderecoCompleto }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ trim(($this->estacao->bairro ?? '').' - '.$cidadeUf, ' -') ?: '—' }}
                            </p>
                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                                @if ($this->estacao->cep)
                                    <span>{{ __('CEP') }}: <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $this->estacao->cep }}</span></span>
                                @endif
                                @if ($this->estacao->complemento)
                                    <span>{{ __('Complemento') }}: <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $this->estacao->complemento }}</span></span>
                                @endif
                                @if ($this->estacao->regional)
                                    <span>{{ __('Regional') }}: <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $this->estacao->regional }}</span></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($this->estacao->latitude !== null && $this->estacao->longitude !== null)
                        <div class="shrink-0 sm:text-right">
                            <div class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-100 px-3.5 py-2.5 text-xs font-medium tabular-nums text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                                <flux:icon.globe-americas class="size-4 text-zinc-400 dark:text-zinc-500" />
                                <span>{{ $fmtNumber($this->estacao->latitude, 6) }}, {{ $fmtNumber($this->estacao->longitude, 6) }}</span>
                            </div>
                            <a
                                href="https://www.google.com/maps?q={{ $this->estacao->latitude }},{{ $this->estacao->longitude }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-sky-600 transition-colors hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300"
                            >
                                <flux:icon.arrow-top-right-on-square class="size-3.5" />
                                {{ __('Abrir no mapa') }}
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Estrutura --}}
            <section id="estrutura" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                        <flux:icon.server-stack class="size-4.5" />
                    </div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Estrutura') }}</h3>
                </header>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => __('Tipo da torre'), 'value' => $this->estacao->tipo_torre],
                        ['label' => __('AEV Nominal'), 'value' => $fmtNumber($this->estacao->aev_nominal)],
                        ['label' => __('Área de solo (m²)'), 'value' => $fmtNumber($this->estacao->area_solo)],
                        ['label' => __('Altura (m)'), 'value' => $fmtNumber($this->estacao->altura_estrutura)],
                    ] as $item)
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                            <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Ciclo de vida --}}
            <section id="vida" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                            <flux:icon.calendar-days class="size-4.5" />
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Ciclo de vida') }}</h3>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                        {{ $progress }}%
                    </span>
                </header>

                {{-- Progress summary --}}
                <div class="mb-6 flex items-center gap-4 rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                    <div class="relative size-16 shrink-0">
                        <svg class="size-16 -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.5" class="stroke-zinc-200 dark:stroke-white/10" />
                            <circle
                                cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.5" stroke-linecap="round"
                                stroke-dasharray="{{ $progress }} {{ 100 - $progress }}"
                                class="stroke-emerald-500 transition-all duration-700"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-bold text-zinc-900 dark:text-white">{{ $progress }}%</div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ $completedSteps }} {{ __('de') }} {{ count($lifecycle) }} {{ __('etapas concluídas') }}
                        </p>
                        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Progresso do ciclo de vida da estação') }}</p>
                    </div>
                </div>

                {{-- Timeline --}}
                <ol class="relative">
                    @foreach ($lifecycle as $index => $step)
                        @php
                            $isDone = $step['date'] !== null;
                            $isCurrent = $index === $currentStep;
                            $isLast = $index === count($lifecycle) - 1;
                            $nextDone = isset($lifecycle[$index + 1]['date']) && $lifecycle[$index + 1]['date'] !== null;
                        @endphp
                        <li class="relative flex gap-4 pb-6 last:pb-0">
                            @if (! $isLast)
                                <span aria-hidden="true" class="absolute left-[18px] top-9 h-[calc(100%-2.25rem)] w-px {{ $nextDone ? 'bg-emerald-400/50' : 'bg-zinc-200 dark:bg-white/10' }}"></span>
                            @endif

                            <div class="relative flex size-9 shrink-0 items-center justify-center rounded-xl {{ $isDone ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : ($isCurrent ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/30 ring-4 ring-sky-500/15' : 'bg-zinc-100 text-zinc-400 dark:bg-white/5 dark:text-zinc-500') }}">
                                <flux:icon :icon="$step['icon']" class="size-4" />
                            </div>

                            <div class="min-w-0 pt-0.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $step['label'] }}</p>
                                    @if ($isCurrent)
                                        <span class="inline-flex items-center rounded-full bg-sky-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                            {{ __('Atual') }}
                                        </span>
                                    @elseif ($isDone)
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                                            {{ __('Concluído') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm font-semibold {{ $isDone ? 'text-zinc-900 dark:text-white' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $fmtDate($step['date']) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Contratos --}}
            <section id="contratos" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-zinc-700/10 text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                        <flux:icon.clipboard-document-list class="size-4.5" />
                    </div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Contratos') }}</h3>
                </header>

                <dl class="flex flex-col">
                    @foreach ([
                        ['label' => __('Contrato da Área'), 'value' => $this->estacao->tipo_contrato_area],
                        ['label' => __('Detentor da Área'), 'value' => $this->estacao->detentor_area],
                        ['label' => __('Contrato Infra'), 'value' => $this->estacao->tipo_contrato_infra],
                        ['label' => __('Detentor de Infra'), 'value' => $this->estacao->detentor_infra],
                        ['label' => __('Tipo de Infra'), 'value' => $this->estacao->tipo_infra],
                        ['label' => __('Tipo de EV'), 'value' => $this->estacao->tipo_ev],
                        ['label' => __('Fornecedor de EV'), 'value' => $this->estacao->fornecedor_ev],
                    ] as $index => $item)
                        <div class="flex items-baseline justify-between gap-4 border-b border-zinc-100 py-3 first:pt-0 last:border-b-0 last:pb-0 dark:border-white/5">
                            <dt class="shrink-0 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</dt>
                            <dd class="min-w-0 truncate text-right text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            {{-- Anotações --}}
            <section id="anotacoes" data-section class="scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <header class="mb-6 flex items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                        <flux:icon.document-text class="size-4.5" />
                    </div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Anotações') }}</h3>
                </header>

                @php
                    $anotacoes = collect([
                        ['label' => __('Situação'), 'value' => $this->estacao->situacao, 'icon' => 'flag'],
                        ['label' => __('OTs'), 'value' => $this->estacao->ots, 'icon' => 'list-bullet'],
                        ['label' => __('Observação'), 'value' => $this->estacao->observacao, 'icon' => 'chat-bubble-left-ellipsis'],
                        ['label' => __('Justificativa'), 'value' => $this->estacao->justificativa, 'icon' => 'document-check'],
                        ['label' => __('Observação THQ'), 'value' => $this->estacao->observacao_thq, 'icon' => 'paper-clip'],
                    ])->filter(fn ($item) => $item['value'] !== null && trim((string) $item['value']) !== '');
                @endphp

                @if ($anotacoes->isEmpty())
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">{{ __('Nenhuma anotação registrada.') }}</p>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($anotacoes as $item)
                            <div class="rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                                <p class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                    <flux:icon :icon="$item['icon']" variant="micro" class="size-3.5" />
                                    {{ $item['label'] }}
                                </p>
                                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $item['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- Anexos --}}
    <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 200ms">
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
                <x-ui.file-icon :mime="$anexo->mime" container="size-10" icon="size-5" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $anexo->nome }}</p>
                    <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $fmtBytes($anexo->tamanho) }} · {{ $anexo->created_at?->format('d/m/Y') }}
                    </p>
                </div>
                <a
                    href="{{ route('estacoes.anexos.download', $anexo) }}"
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
                {{ __('Nenhum anexo registrado. Adicione contratos, laudos ou documentos da estação.') }}
            </p>
        @endforelse
    </div>
</section>

    {{-- Comentários --}}
    <section id="comentarios" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 240ms">
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
            <flux:textarea wire:model="comentario" rows="3" placeholder="{{ __('Escreva um comentário sobre a estação...') }}" />
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
        <flux:heading level="2">{{ __('Excluir estação') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->estacao->site_id }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif