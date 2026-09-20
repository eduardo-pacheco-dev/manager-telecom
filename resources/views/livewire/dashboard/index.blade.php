<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    @php
        $statusCor = [
            'CANDIDATO A' => ['badge' => 'bg-teal-500/15 text-teal-700 dark:text-teal-400', 'barra' => 'bg-teal-500 dark:bg-teal-400'],
            'Aquisitado' => ['badge' => 'bg-amber-500/15 text-amber-700 dark:text-amber-400', 'barra' => 'bg-amber-500 dark:bg-amber-400'],
            'Adquirido' => ['badge' => 'bg-sky-500/15 text-sky-700 dark:text-sky-400', 'barra' => 'bg-sky-500 dark:bg-sky-400'],
            'Em construção' => ['badge' => 'bg-violet-500/15 text-violet-700 dark:text-violet-400', 'barra' => 'bg-violet-500 dark:bg-violet-400'],
            'Ativo' => ['badge' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400', 'barra' => 'bg-emerald-500 dark:bg-emerald-400'],
            'Inativo' => ['badge' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400', 'barra' => 'bg-zinc-400 dark:bg-zinc-500'],
            'Desativado' => ['badge' => 'bg-slate-500/15 text-slate-600 dark:text-slate-400', 'barra' => 'bg-slate-400 dark:bg-slate-500'],
            'Cancelado' => ['badge' => 'bg-rose-500/15 text-rose-700 dark:text-rose-400', 'barra' => 'bg-rose-500 dark:bg-rose-400'],
            'Sem status' => ['badge' => 'bg-zinc-500/10 text-zinc-500 dark:bg-white/5 dark:text-zinc-400', 'barra' => 'bg-zinc-300 dark:bg-zinc-600'],
        ];
        $statusPadrao = ['badge' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400', 'barra' => 'bg-zinc-400 dark:bg-zinc-500'];

        $tecnologiaCor = [
            'GSM' => ['badge' => 'bg-rose-500/15 text-rose-700 dark:text-rose-400', 'barra' => 'bg-rose-500'],
            'UMTS' => ['badge' => 'bg-amber-500/15 text-amber-700 dark:text-amber-400', 'barra' => 'bg-amber-500'],
            'LTE' => ['badge' => 'bg-sky-500/15 text-sky-700 dark:text-sky-400', 'barra' => 'bg-sky-500'],
            '5G NR' => ['badge' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400', 'barra' => 'bg-emerald-500'],
        ];
        $tecnologiaPadrao = ['badge' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400', 'barra' => 'bg-zinc-400'];

        $osStatusCor = [
            'Aberta' => ['badge' => 'bg-sky-500/15 text-sky-700 dark:text-sky-400', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
            'Em andamento' => ['badge' => 'bg-amber-500/15 text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
            'Aguardando' => ['badge' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
            'Concluída' => ['badge' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
            'Cancelada' => ['badge' => 'bg-rose-500/15 text-rose-700 dark:text-rose-400', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
        ];
        $osStatusPadrao = ['badge' => 'bg-zinc-500/15 text-zinc-600 dark:text-zinc-400', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

        $osPrioridadeCor = [
            'Baixa' => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300',
            'Média' => 'bg-sky-500/15 text-sky-700 dark:text-sky-400',
            'Alta' => 'bg-amber-500/15 text-amber-700 dark:text-amber-400',
            'Urgente' => 'bg-rose-500/15 text-rose-700 dark:text-rose-400',
        ];
        $osPrioridadePadrao = 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';

        $tints = [
            'bg-sky-500/15 text-sky-700 dark:text-sky-300',
            'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
            'bg-violet-500/15 text-violet-700 dark:text-violet-300',
            'bg-amber-500/15 text-amber-700 dark:text-amber-300',
            'bg-rose-500/15 text-rose-700 dark:text-rose-300',
            'bg-teal-500/15 text-teal-700 dark:text-teal-300',
            'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300',
        ];

        $total = (int) $this->stats['estacoes'];
        $radioLinks = (int) $this->stats['radioLinks'];
        $radioLinksAtivos = (int) $this->stats['radioLinksAtivos'];
        $pctRadioAtivos = $radioLinks > 0 ? (int) round(($radioLinksAtivos / $radioLinks) * 100) : 0;

        $ordens = (int) $this->stats['ordens'];
        $ordensAbertas = (int) $this->stats['ordensAbertas'];
        $pctOrdensAbertas = $ordens > 0 ? (int) round(($ordensAbertas / $ordens) * 100) : 0;
    @endphp

    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Dashboard')"
        :subtitle="$this->saudacao().', '.auth()->user()->name.'. '.now()->translatedFormat('l, d \d\e F \d\e Y').'.'"
        :breadcrumbs="[
            ['label' => __('Platform'), 'href' => null],
            ['label' => __('Dashboard'), 'href' => null],
        ]"
    />

    {{-- Indicadores --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card
            :label="__('Estações')"
            :value="$total"
            icon="signal"
            color="zinc"
            :progress="$this->stats['cobertura']"
            :footnote="__('em').' '.$this->stats['municipios'].' '.trans_choice('município|municípios', $this->stats['municipios'])"
        />

        <x-ui.stat-card
            :label="__('Radio Links')"
            :value="$radioLinks"
            icon="radio"
            color="sky"
            :progress="$pctRadioAtivos"
            :footnote="$radioLinksAtivos.' '.__('ativos')"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Ordens de Serviço')"
            :value="$ordens"
            icon="clipboard-document-list"
            color="emerald"
            :progress="$pctOrdensAbertas"
            :footnote="$ordensAbertas.' '.__('em aberto')"
            delay="140ms"
        />

        <x-ui.stat-card
            :label="__('Clientes')"
            :value="$this->stats['clientes']"
            icon="building-office"
            color="violet"
            :footnote="$this->stats['clientesAtivos'].' '.__('ativos')"
            delay="190ms"
        />
    </div>

    {{-- Visão geral da rede --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03] lg:col-span-2" style="animation-delay: 240ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Situação da rede') }}</flux:heading>
                    <flux:subheading class="mt-1">{{ __('Estações por status de implantação') }}</flux:subheading>
                </div>
                <flux:button href="{{ route('estacoes.index') }}" wire:navigate variant="ghost" size="sm" icon:trailing="arrow-right">
                    {{ __('Ver todas') }}
                </flux:button>
            </div>

            @if ($this->estacoesPorStatus->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.signal-slash class="size-6 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">{{ __('Nenhuma estação registrada ainda') }}</p>
                    <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-4">
                        {{ __('Cadastrar estação') }}
                    </flux:button>
                </div>
            @else
                <div class="mt-4 divide-y divide-zinc-100 dark:divide-white/5">
                    @foreach ($this->estacoesPorStatus as $item)
                        @php $cor = $statusCor[$item['rotulo']] ?? $statusPadrao; @endphp

                        <div class="flex items-center gap-3 py-2.5">
                            <span class="size-2 shrink-0 rounded-full {{ $cor['barra'] }}" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1 truncate text-sm text-zinc-600 dark:text-zinc-300">{{ $item['rotulo'] }}</span>
                            <span class="text-sm font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $item['total'] }}</span>
                            <div class="hidden h-2 w-32 shrink-0 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10 sm:block" role="presentation">
                                <div class="h-full rounded-full {{ $cor['barra'] }}" style="width: {{ $item['percentual'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
            <div>
                <flux:heading size="lg">{{ __('Tecnologias') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Distribuição por padrão de rede') }}</flux:subheading>
            </div>

            @if ($this->estacoesPorTecnologia->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.radio class="size-6 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">{{ __('Sem dados de tecnologia') }}</p>
                </div>
            @else
                <div class="mt-4 space-y-2.5">
                    @foreach ($this->estacoesPorTecnologia as $item)
                        @php $cor = $tecnologiaCor[$item['rotulo']] ?? $tecnologiaPadrao; @endphp

                        <div>
                            <div class="flex items-center gap-3">
                                <span class="size-2 shrink-0 rounded-full {{ $cor['barra'] }}" aria-hidden="true"></span>
                                <span class="min-w-0 flex-1 truncate text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $item['rotulo'] }}</span>
                                <span class="text-sm tabular-nums text-zinc-500 dark:text-zinc-400">{{ $item['total'] }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                                <div class="h-full rounded-full {{ $cor['barra'] }}" style="width: {{ $item['percentual'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Acesso rápido + últimas estações --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 320ms">
            <flux:heading size="lg">{{ __('Acesso rápido') }}</flux:heading>
            <flux:subheading class="mt-1">{{ __('Módulos do sistema') }}</flux:subheading>

            <div class="mt-3 space-y-0.5">
                @foreach ($this->modulos as $modulo)
                    <a
                        href="{{ $modulo['rota'] }}"
                        wire:navigate
                        class="group flex cursor-pointer items-center gap-3 rounded-xl px-2 py-2.5 transition-colors hover:bg-zinc-50 dark:hover:bg-white/5"
                    >
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $tints[$loop->index % count($tints)] }}">
                            <flux:icon :icon="$modulo['icone']" class="size-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $modulo['nome'] }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $modulo['descricao'] }}</p>
                        </div>
                        <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-zinc-100 px-1.5 py-0.5 text-xs font-semibold tabular-nums text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                            {{ $modulo['contagem'] }}
                        </span>
                        <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-300 transition-transform group-hover:translate-x-0.5 group-hover:text-zinc-400 dark:text-zinc-600 dark:group-hover:text-zinc-400" />
                    </a>
                @endforeach
            </div>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03] lg:col-span-2" style="animation-delay: 360ms">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Últimas estações') }}</flux:heading>
                    <flux:subheading class="mt-1">{{ __('Atualizadas mais recentemente') }}</flux:subheading>
                </div>
                <flux:button href="{{ route('estacoes.index') }}" wire:navigate variant="ghost" size="sm" icon:trailing="arrow-right">
                    {{ __('Ver todas') }}
                </flux:button>
            </div>

            @if ($this->ultimasEstacoes->isEmpty())
                <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                    <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.signal-slash class="size-6 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">{{ __('Nenhuma estação registrada ainda') }}</p>
                    <flux:button href="{{ route('estacoes.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-4">
                        {{ __('Cadastrar estação') }}
                    </flux:button>
                </div>
            @else
                <div class="mt-4">
                    @foreach ($this->ultimasEstacoes as $estacao)
                        @php
                            $statusBadge = $statusCor[$estacao->status ?? 'Sem status']['badge'] ?? $statusPadrao['badge'];
                            $tipoBadge = $tecnologiaCor[$estacao->tecnologia]['badge'] ?? $tecnologiaPadrao['badge'];
                        @endphp

                        <a
                            href="{{ route('estacoes.show', $estacao) }}"
                            wire:navigate
                            class="group flex cursor-pointer items-center gap-4 border-b border-zinc-100 px-1 py-3 transition-colors last:border-b-0 hover:bg-zinc-50/80 dark:border-white/5 dark:hover:bg-white/[0.02]"
                        >
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                                <flux:icon.radio class="size-4" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $estacao->site_id }}</p>
                                    @if ($estacao->situacao)
                                        <span class="shrink-0 truncate rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 dark:bg-white/10 dark:text-zinc-300">{{ $estacao->situacao }}</span>
                                    @endif
                                </div>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $estacao->municipio ?: __('Sem município') }}@if ($estacao->estado) · {{ $estacao->estado }}@endif
                                </p>
                            </div>

                            @if ($estacao->tecnologia)
                                <span class="hidden shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium sm:inline-flex {{ $tipoBadge }}">{{ $estacao->tecnologia }}</span>
                            @endif

                            <span class="hidden shrink-0 text-xs text-zinc-400 dark:text-zinc-500 lg:block">{{ $estacao->updated_at->format('d/m/Y') }}</span>

                            <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-300 transition-transform group-hover:translate-x-0.5 dark:text-zinc-600" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Últimas ordens de serviço --}}
    <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 400ms">
        <div class="flex items-center justify-between gap-3">
            <div>
                <flux:heading size="lg">{{ __('Últimas ordens de serviço') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Ordens abertas mais recentemente') }}</flux:subheading>
            </div>
            <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost" size="sm" icon:trailing="arrow-right">
                {{ __('Ver todas') }}
            </flux:button>
        </div>

        @if ($this->ultimasOrdens->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                    <flux:icon.clipboard-document-list class="size-6 text-zinc-400 dark:text-zinc-500" />
                </div>
                <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">{{ __('Nenhuma ordem de serviço registrada ainda') }}</p>
                <flux:button href="{{ route('ordens-servico.create') }}" wire:navigate variant="primary" size="sm" icon="plus" class="mt-4">
                    {{ __('Nova ordem') }}
                </flux:button>
            </div>
        @else
            <div class="mt-4 grid gap-3 lg:grid-cols-2">
                @foreach ($this->ultimasOrdens as $ordem)
                    @php
                        $osStatus = $osStatusCor[$ordem->status] ?? $osStatusPadrao;
                        $osPrioridade = $osPrioridadeCor[$ordem->prioridade] ?? $osPrioridadePadrao;
                    @endphp

                    <a
                        href="{{ route('ordens-servico.show', $ordem) }}"
                        wire:navigate
                        class="group flex cursor-pointer items-center gap-4 rounded-xl border border-zinc-100 bg-zinc-50/60 px-4 py-3 transition-colors hover:border-sky-200 hover:bg-sky-50/60 dark:border-white/5 dark:bg-white/[0.02] dark:hover:border-sky-400/20 dark:hover:bg-sky-400/5"
                    >
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                            <flux:icon.clipboard-document-list class="size-5" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $ordem->codigo }}</p>
                                @if ($ordem->prioridade)
                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $osPrioridade }}">{{ $ordem->prioridade }}</span>
                                @endif
                            </div>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $ordem->titulo }}@if ($ordem->radioLink) · {{ $ordem->radioLink->codigo }}@endif
                            </p>
                        </div>

                        <div class="flex shrink-0 flex-col items-end gap-1.5">
                            @if ($ordem->status)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $osStatus['badge'] }}">
                                    <span class="size-1.5 shrink-0 rounded-full {{ $osStatus['dot'] }}"></span>
                                    {{ $ordem->status }}
                                </span>
                            @endif
                            @if ($ordem->data_abertura)
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $ordem->data_abertura->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-300 transition-transform group-hover:translate-x-0.5 dark:text-zinc-600" />
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>