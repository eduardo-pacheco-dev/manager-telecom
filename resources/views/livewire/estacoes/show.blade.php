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
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <flux:button
                href="{{ route('estacoes.index') }}"
                wire:navigate
                icon="arrow-left"
                variant="ghost"
                size="sm"
                :aria-label="__('Voltar para estações')"
            />

            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Estação') }}</p>
                <flux:heading size="xl" level="1" class="truncate">{{ $this->estacao->site_id }}</flux:heading>
            </div>

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

        <flux:separator variant="subtle" />
    </div>

    {{-- Hero summary --}}
    <div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50/80 p-6 dark:border-white/10 dark:from-white/[0.04] dark:to-white/[0.02]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-16 -top-16 size-56 rounded-full bg-sky-500/5 blur-3xl dark:bg-sky-400/10"></div>
        <div class="pointer-events-none absolute -bottom-20 left-1/3 size-48 rounded-full bg-violet-500/5 blur-3xl dark:bg-violet-400/10"></div>

        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-violet-500 text-sm font-bold tracking-wide text-white shadow-lg shadow-sky-500/25">
                    {{ \Illuminate\Support\Str::limit($this->estacao->site_id, 5, '') }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->estacao->site_id }}</h2>
                        @if ($this->estacao->status)
                            <flux:badge :color="$statusBadgeColor" rounded>{{ $this->estacao->status }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-1.5 truncate text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $this->estacao->tipo_elemento ?? '—' }}@if ($this->estacao->tecnologia) · {{ $this->estacao->tecnologia }}@endif@if ($this->estacao->municipio) · {{ $this->estacao->municipio }}{{ $this->estacao->estado ? ' - '.$this->estacao->estado : '' }}@endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-12 gap-y-5 sm:grid-cols-4 lg:gap-x-14">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Classificação') }}</p>
                    @if ($this->estacao->classificacao)
                        <flux:badge :color="$classificacaoBadgeColor" rounded class="mt-1.5">{{ $this->estacao->classificacao }}</flux:badge>
                    @else
                        <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">—</p>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Município') }}</p>
                    <p class="mt-1.5 truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $this->estacao->municipio ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Regional') }}</p>
                    <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $this->estacao->regional ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Aquisição') }}</p>
                    <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_aquisicao) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sections --}}
    <div class="animate-fade-in-up flex flex-col gap-8" style="animation-delay: 80ms">
        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Identificação') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de elemento') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_elemento ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tecnologia') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tecnologia ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de conexão') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_conexao ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Classificação') }}</dt>
                    <dd class="mt-1.5">
                        @if ($this->estacao->classificacao)
                            <flux:badge :color="$classificacaoBadgeColor" rounded>{{ $this->estacao->classificacao }}</flux:badge>
                        @else
                            <span class="text-sm text-zinc-900 dark:text-white">—</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Endereço ID') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->endereco_id ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Station ID') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->station_id ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Ordem Complexa') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->ordem_complexa ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Status') }}</dt>
                    <dd class="mt-1.5">
                        @if ($this->estacao->status)
                            <flux:badge :color="$statusBadgeColor" rounded>{{ $this->estacao->status }}</flux:badge>
                        @else
                            <span class="text-sm text-zinc-900 dark:text-white">—</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Datas') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-3 lg:grid-cols-5">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Aquisição') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_aquisicao) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Construção') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_construcao) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Ativação') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_ativacao) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Desativação') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_desativacao) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Cancelamento') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtDate($this->estacao->data_cancelamento) }}</dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Contratos e infraestrutura') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de contrato da Área') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_contrato_area ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Detentor da Área') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->detentor_area ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de contrato Infra') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_contrato_infra ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Detentor de Infra') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->detentor_infra ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de Infra') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_infra ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo de EV') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_ev ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Fornecedor de EV') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->fornecedor_ev ?? '—' }}</dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Endereço') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Logradouro') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">
                        {{ trim(($this->estacao->tipo_logradouro ?? '').' '.($this->estacao->logradouro ?? '')) ?: '—' }}
                        @if ($this->estacao->numero), {{ $this->estacao->numero }}@endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Complemento') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->complemento ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Bairro') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->bairro ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Município') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->municipio ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Estado') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->estado ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('CEP') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->cep ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Regional') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->regional ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Latitude') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtNumber($this->estacao->latitude, 6) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Longitude') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtNumber($this->estacao->longitude, 6) }}</dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Estrutura') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Tipo da torre') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->tipo_torre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('AEV Nominal') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtNumber($this->estacao->aev_nominal) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Área de solo (m²)') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtNumber($this->estacao->area_solo) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Altura da estrutura (m)') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $fmtNumber($this->estacao->altura_estrutura) }}</dd>
                </div>
            </dl>
        </section>

        <section class="flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <flux:heading size="lg" class="shrink-0">{{ __('Informações adicionais') }}</flux:heading>
                <div class="h-px flex-1 bg-zinc-100 dark:bg-white/5"></div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Situação') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->situacao ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('OTs') }}</dt>
                    <dd class="mt-1.5 text-sm text-zinc-900 dark:text-white">{{ $this->estacao->ots ?? '—' }}</dd>
                </div>
            </dl>

            @if ($this->estacao->observacao)
                <div class="rounded-xl border border-zinc-200 p-5 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Observação') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->estacao->observacao }}</p>
                </div>
            @endif

            @if ($this->estacao->justificativa)
                <div class="rounded-xl border border-zinc-200 p-5 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Justificativa') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->estacao->justificativa }}</p>
                </div>
            @endif

            @if ($this->estacao->observacao_thq)
                <div class="rounded-xl border border-zinc-200 p-5 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Observação THQ') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->estacao->observacao_thq }}</p>
                </div>
            @endif
        </section>
    </div>
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