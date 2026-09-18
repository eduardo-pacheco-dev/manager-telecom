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

        $tints = [
            'bg-sky-500/15 text-sky-700 dark:text-sky-300',
            'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
            'bg-violet-500/15 text-violet-700 dark:text-violet-300',
            'bg-amber-500/15 text-amber-700 dark:text-amber-300',
            'bg-rose-500/15 text-rose-700 dark:text-rose-300',
            'bg-teal-500/15 text-teal-700 dark:text-teal-300',
        ];
    @endphp

    {{-- Cabeçalho --}}
    <div class="animate-fade-in-up flex flex-col gap-5">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>{{ __('Platform') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Dashboard') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
                <flux:subheading size="lg" class="mt-1.5">
                    {{ $this->saudacao() }},
                    <span class="font-medium text-zinc-900 dark:text-white">{{ auth()->user()->name }}</span>.
                    <span class="capitalize">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}.</span>
                </flux:subheading>
            </div>
        </div>
    </div>

    {{-- Indicadores --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Estações') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['estacoes'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.signal class="size-5" />
                </div>
            </div>
            <p class="mt-3 truncate text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('em') }} {{ $this->stats['municipios'] }} {{ trans_choice('município|municípios', $this->stats['municipios']) }}
            </p>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Em operação') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['emOperacao'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.check-circle class="size-5" />
                </div>
            </div>
            <div class="mt-3">
                <div class="h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $this->stats['cobertura'] }}%"></div>
                </div>
                <p class="mt-1.5 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->stats['cobertura'] }}% {{ __('da rede em operação') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Colaboradores') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['colaboradores'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.users class="size-5" />
                </div>
            </div>
            <p class="mt-3 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->stats['colaboradoresAtivos'] }} {{ __('ativos') }}</p>
        </div>

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Serviços') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['servicos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                    <flux:icon.wrench-screwdriver class="size-5" />
                </div>
            </div>
            <p class="mt-3 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->stats['servicosAtivos'] }} {{ __('ativos') }}</p>
        </div>
    </div>

    {{-- Visão geral da rede --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5 lg:col-span-2" style="animation-delay: 240ms">
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

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 280ms">
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
        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5" style="animation-delay: 320ms">
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

        <div class="animate-fade-in-up rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-white/5 lg:col-span-2" style="animation-delay: 360ms">
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
</div>