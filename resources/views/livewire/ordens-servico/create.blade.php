<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Nova Ordem de Serviço')"
        :subtitle="__('Preencha os dados para cadastrar uma nova ordem')"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Ordens de Serviço'), 'href' => route('ordens-servico.index')],
            ['label' => __('Nova'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            @php
                $linkSelecionado = $radioLinks->firstWhere('id', (int) $radio_link_id);
                $estacaoSelecionada = $estacoes->firstWhere('id', (int) $estacao_a_id);
            @endphp

            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Dados básicos da ordem de serviço')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Código Personalizado') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="titulo" type="text" required autofocus icon="tag" :placeholder="__('Ex.: Manutenção preventiva no link principal')" />
                            <flux:error name="titulo" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Tipo') }}</flux:label>
                            <flux:select wire:model="tipo">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\OrdemServico::tiposDisponiveis() as $tipo)
                                    <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Código do cliente') }}</flux:label>
                            <flux:input wire:model="codigo_cliente" type="text" />
                            <flux:error name="codigo_cliente" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Cliente') }}</flux:label>
                            <flux:select wire:model="cliente_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($clientes as $cliente)
                                    <flux:select.option :value="$cliente->id">{{ $cliente->nome }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="cliente_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Ordem complexa') }}</flux:label>
                            <flux:input wire:model="ordem_complexa" type="text" />
                            <flux:error name="ordem_complexa" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Vínculo --}}
            <section id="enlace" data-section class="animate-fade-in-up relative z-20 scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="radio"
                    :title="__('Vínculo')"
                    :description="__('Defina o escopo da ordem e os responsáveis')"
                >
                    <flux:field>
                        <flux:label>{{ __('Escopo') }}</flux:label>
                        <flux:radio.group variant="cards" wire:model.live="escopo" class="flex-wrap">
                            <flux:radio variant="cards" value="Enlace" icon="radio" label="{{ __('Enlace') }}" description="{{ __('Ordem vinculada a um radio link') }}" />
                            <flux:radio variant="cards" value="Estação" icon="signal" label="{{ __('Estação') }}" description="{{ __('Ordem somente relacionada a uma estação') }}" />
                            <flux:radio variant="cards" value="Outro" icon="document-text" label="{{ __('Outro') }}" description="{{ __('Sem vínculo com enlace ou estação') }}" />
                        </flux:radio.group>
                        <flux:error name="escopo" />
                    </flux:field>

                    @if ($escopo === 'Enlace')
                        <flux:field>
                            <flux:label>{{ __('Radio Link') }} <span class="text-rose-500">*</span></flux:label>

                            <div x-data="{ open: false }" class="relative">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between gap-2 rounded-xl border border-dashed border-violet-300 bg-white px-3.5 py-3 text-left text-sm shadow-sm transition-colors hover:border-violet-400 hover:bg-violet-50/40 dark:border-white/15 dark:bg-white/5 dark:hover:border-violet-400/50 dark:hover:bg-violet-400/5"
                                    :class="open ? 'ring-2 ring-accent ring-offset-2' : ''"
                                >
                                    @if ($linkSelecionado)
                                        <span class="flex min-w-0 items-center gap-2">
                                            <flux:icon.radio class="size-4 shrink-0 text-violet-500 dark:text-violet-400" />
                                            <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $linkSelecionado->codigo }}</span>
                                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $linkSelecionado->estacaoA->site_id }} → {{ $linkSelecionado->estacaoB->site_id }}</span>
                                        </span>
                                    @else
                                        <span class="flex items-center gap-2 text-zinc-400 dark:text-zinc-500">
                                            <flux:icon.radio class="size-4" />
                                            {{ __('Selecione um radio link...') }}
                                        </span>
                                    @endif
                                    <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 mt-1.5 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl shadow-black/5 dark:border-white/10 dark:bg-zinc-900"
                                >
                                    <div class="border-b border-zinc-100 p-2 dark:border-white/5">
                                        <flux:input
                                            wire:model.live="buscaRadioLink"
                                            :placeholder="__('Buscar por código ou site...')"
                                            icon="magnifying-glass"
                                            size="sm"
                                        />
                                    </div>

                                    <div class="flex max-h-64 flex-col divide-y divide-zinc-100 overflow-y-auto dark:divide-white/5">
                                        @if ($this->radioLinksEncontrados->isNotEmpty())
                                            @foreach ($this->radioLinksEncontrados as $radioLink)
                                                <button
                                                    type="button"
                                                    wire:key="radio-link-{{ $radioLink->id }}"
                                                    wire:click="selectRadioLink({{ $radioLink->id }})"
                                                    @click="open = false"
                                                    class="{{ (string) $radioLink->id === $radio_link_id ? 'bg-violet-50/60 dark:bg-violet-400/5' : '' }} flex w-full cursor-pointer items-center gap-3 px-3.5 py-2.5 text-left transition-colors hover:bg-zinc-50 dark:hover:bg-white/5"
                                                >
                                                    <span class="{{ (string) $radioLink->id === $radio_link_id ? 'border-violet-500 bg-violet-500 text-white' : 'border-zinc-300 bg-white dark:border-white/20' }} flex size-5 shrink-0 items-center justify-center rounded-full border transition-colors">
                                                        @if ((string) $radioLink->id === $radio_link_id)
                                                            <flux:icon.check class="size-3.5" />
                                                        @endif
                                                    </span>
                                                    <span class="min-w-0 flex-1">
                                                        <span class="block truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $radioLink->codigo }}</span>
                                                        <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">
                                                            {{ $radioLink->estacaoA->site_id }} → {{ $radioLink->estacaoB->site_id }}
                                                        </span>
                                                    </span>
                                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-[11px] font-medium text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">
                                                        <flux:icon.radio class="size-3" />
                                                        {{ $radioLink->status ?: '—' }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        @else
                                            <p class="px-3.5 py-4 text-center text-sm text-zinc-400 dark:text-zinc-500">
                                                {{ __('Nenhum radio link encontrado.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <flux:error name="radio_link_id" />
                        </flux:field>

                        {{-- Link selecionado: visual A → B --}}
                        @if ($linkSelecionado)
                            <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                                <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                        <flux:icon.map-pin class="size-4.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação A') }}</p>
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoA->site_id }}</p>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center justify-center gap-2 px-1">
                                    <flux:icon.arrow-right class="size-4 text-violet-500 dark:text-violet-400" />
                                    <span class="rounded-full bg-violet-500/10 px-2.5 py-0.5 text-xs font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">{{ $linkSelecionado->codigo }}</span>
                                </div>

                                <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                        <flux:icon.map-pin class="size-4.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação B') }}</p>
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoB->site_id }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-4 py-3 dark:border-white/10 dark:bg-white/[0.02]">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500">
                                    <flux:icon.sparkles class="size-4.5" />
                                </div>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('Selecione um radio link para preencher as estações A/B automaticamente.') }}
                                </p>
                            </div>
                        @endif

                        {{-- Estações (hidden fields) --}}
                        <input type="hidden" wire:model="estacao_a_id" />
                        <input type="hidden" wire:model="estacao_b_id" />
                    @elseif ($escopo === 'Estação')
                        <flux:field>
                            <flux:label>{{ __('Estação') }} <span class="text-rose-500">*</span></flux:label>

                            <div x-data="{ open: false }" class="relative">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between gap-2 rounded-xl border border-dashed border-sky-300 bg-white px-3.5 py-3 text-left text-sm shadow-sm transition-colors hover:border-sky-400 hover:bg-sky-50/40 dark:border-white/15 dark:bg-white/5 dark:hover:border-sky-400/50 dark:hover:bg-sky-400/5"
                                    :class="open ? 'ring-2 ring-accent ring-offset-2' : ''"
                                >
                                    @if ($estacaoSelecionada)
                                        <span class="flex min-w-0 items-center gap-2">
                                            <flux:icon.map-pin class="size-4 shrink-0 text-sky-500 dark:text-sky-400" />
                                            <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $estacaoSelecionada->site_id }}</span>
                                            <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacaoSelecionada->municipio ?: $estacaoSelecionada->endereco_id }}</span>
                                        </span>
                                    @else
                                        <span class="flex items-center gap-2 text-zinc-400 dark:text-zinc-500">
                                            <flux:icon.map-pin class="size-4" />
                                            {{ __('Selecione uma estação...') }}
                                        </span>
                                    @endif
                                    <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 mt-1.5 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl shadow-black/5 dark:border-white/10 dark:bg-zinc-900"
                                >
                                    <div class="border-b border-zinc-100 p-2 dark:border-white/5">
                                        <flux:input
                                            wire:model.live="buscaEstacao"
                                            :placeholder="__('Buscar por site ID, endereço ou município...')"
                                            icon="magnifying-glass"
                                            size="sm"
                                        />
                                    </div>

                                    <div class="flex max-h-64 flex-col divide-y divide-zinc-100 overflow-y-auto dark:divide-white/5">
                                        @if ($this->estacoesEncontradas->isNotEmpty())
                                            @foreach ($this->estacoesEncontradas as $estacao)
                                                <button
                                                    type="button"
                                                    wire:key="estacao-{{ $estacao->id }}"
                                                    wire:click="selectEstacao({{ $estacao->id }})"
                                                    @click="open = false"
                                                    class="{{ (string) $estacao->id === $estacao_a_id ? 'bg-sky-50/60 dark:bg-sky-400/5' : '' }} flex w-full cursor-pointer items-center gap-3 px-3.5 py-2.5 text-left transition-colors hover:bg-zinc-50 dark:hover:bg-white/5"
                                                >
                                                    <span class="{{ (string) $estacao->id === $estacao_a_id ? 'border-sky-500 bg-sky-500 text-white' : 'border-zinc-300 bg-white dark:border-white/20' }} flex size-5 shrink-0 items-center justify-center rounded-full border transition-colors">
                                                        @if ((string) $estacao->id === $estacao_a_id)
                                                            <flux:icon.check class="size-3.5" />
                                                        @endif
                                                    </span>
                                                    <span class="min-w-0 flex-1">
                                                        <span class="block truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $estacao->site_id }}</span>
                                                        <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->municipio ?: $estacao->endereco_id }}</span>
                                                    </span>
                                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                                                        <flux:icon.map-pin class="size-3" />
                                                        {{ $estacao->regional ?: $estacao->estado }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        @else
                                            <p class="px-3.5 py-4 text-center text-sm text-zinc-400 dark:text-zinc-500">
                                                {{ __('Nenhuma estação encontrada.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <flux:error name="estacao_a_id" />
                        </flux:field>

                        @if ($estacaoSelecionada)
                            <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/60 px-4 py-3 dark:border-sky-400/20 dark:bg-sky-400/10">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                    <flux:icon.map-pin class="size-4.5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $estacaoSelecionada->site_id }}</p>
                                    <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacaoSelecionada->municipio ?: __('Sem município') }}@if ($estacaoSelecionada->estado) · {{ $estacaoSelecionada->estado }}@endif</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Esta ordem não está vinculada a um enlace ou estação.') }}
                        </p>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Prioridade --}}
            <section id="prioridade" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Prioridade')"
                    :description="__('Defina a urgência da ordem')"
                >
                    <flux:field>
                        <flux:label>{{ __('Prioridade') }}</flux:label>
                        <flux:radio.group variant="cards" wire:model="prioridade" class="flex-wrap">
                            <flux:radio variant="cards" value="Baixa" icon="arrow-down" label="{{ __('Baixa') }}" description="{{ __('Pode ser agendada sem pressa') }}" />
                            <flux:radio variant="cards" value="Média" icon="chart-bar" label="{{ __('Média') }}" description="{{ __('Prioridade padrão de atendimento') }}" />
                            <flux:radio variant="cards" value="Alta" icon="arrow-up" label="{{ __('Alta') }}" description="{{ __('Requer atenção no mesmo dia') }}" />
                            <flux:radio variant="cards" value="Urgente" icon="bolt" label="{{ __('Urgente') }}" description="{{ __('Atendimento imediato') }}" />
                        </flux:radio.group>
                        <flux:error name="prioridade" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Datas previstas para a execução')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Data de abertura') }}</flux:label>
                            <flux:input wire:model="data_abertura" type="date" />
                            <flux:error name="data_abertura" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de conclusão') }}</flux:label>
                            <flux:input wire:model="data_conclusao" type="date" />
                            <flux:error name="data_conclusao" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Descrição --}}
            <section id="descricao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 160ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Descrição')"
                    :description="__('Detalhes e instruções do serviço')"
                >
                    <flux:field>
                        <flux:label>{{ __('Descrição do serviço') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="4" placeholder="{{ __('Descreva o escopo, equipamentos e instruções da ordem...') }}" />
                        <flux:error name="descricao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 200ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Ordem') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['enlace', 'radio', __('Enlace e responsáveis')],
            ['prioridade', 'flag', __('Prioridade')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['descricao', 'document-text', __('Descrição')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Ao selecionar um radio link, as estações A e B são preenchidas automaticamente. Use o status e a prioridade para priorizar o atendimento.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>