<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Novo Projeto TIM')"
        :subtitle="__('Cadastre um novo projeto de implantação e acompanhe as etapas do cronograma')"
        :badge="__('Novo')"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM'), 'href' => route('tim.index')],
            ['label' => __('Novo'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Nome do projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Código Personalizado') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="nome" type="text" required autofocus icon="tag" :placeholder="__('Ex.: Implantação RAN TIM')" />
                            <flux:error name="nome" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Descrição') }}</flux:label>
                            <flux:textarea wire:model="descricao" rows="3" :placeholder="__('Descreva o objetivo e o escopo do projeto...')" />
                            <flux:error name="descricao" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Estação --}}
            <section id="estacao" data-section class="animate-fade-in-up relative z-20 scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="signal"
                    :title="__('Estação')"
                    :description="__('Vincule a estação principal do projeto')"
                >
                    <flux:field>
                        <flux:label>{{ __('Estação') }} <span class="text-rose-500">*</span></flux:label>

                        <div x-data="{ open: false }" class="relative">
                            @if ($this->estacaoSelecionada)
                                <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/60 p-3.5 dark:border-sky-400/20 dark:bg-sky-400/10">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                        <flux:icon.map-pin class="size-5" />
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->estacaoSelecionada->site_id }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ $this->estacaoSelecionada->municipio ?: $this->estacaoSelecionada->endereco_id }}
                                            @if ($this->estacaoSelecionada->regional) · {{ $this->estacaoSelecionada->regional }} @endif
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-sky-700 shadow-sm ring-1 ring-inset ring-sky-200 transition-colors hover:bg-sky-100 dark:bg-white/5 dark:text-sky-300 dark:ring-white/10 dark:hover:bg-white/10"
                                    >
                                        <flux:icon.arrows-up-down class="size-3.5" />
                                        {{ __('Trocar') }}
                                    </button>
                                </div>
                            @endif

                            @if (! $this->estacaoSelecionada || $this->buscaEstacao !== '')
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex w-full items-center justify-between gap-2 rounded-xl border border-dashed border-zinc-300 bg-white px-3.5 py-3 text-left text-sm shadow-sm transition-colors hover:border-sky-400 hover:bg-sky-50/40 dark:border-white/15 dark:bg-white/5 dark:hover:border-sky-400/50 dark:hover:bg-sky-400/5"
                                    :class="open ? 'ring-2 ring-accent ring-offset-2' : ''"
                                >
                                    @if ($this->estacaoSelecionada)
                                        <span class="flex min-w-0 items-center gap-2">
                                            <flux:icon.map-pin class="size-4 shrink-0 text-sky-500 dark:text-sky-400" />
                                            <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $this->estacaoSelecionada->site_id }}</span>
                                        </span>
                                    @else
                                        <span class="flex items-center gap-2 text-zinc-400 dark:text-zinc-500">
                                            <flux:icon.map-pin class="size-4" />
                                            {{ __('Selecione uma estação...') }}
                                        </span>
                                    @endif
                                    <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
                                </button>
                            @endif

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
                                                class="{{ (string) $estacao->id === $this->estacao_id ? 'bg-sky-50/60 dark:bg-sky-400/5' : '' }} flex w-full cursor-pointer items-center gap-3 px-3.5 py-2.5 text-left transition-colors hover:bg-zinc-50 dark:hover:bg-white/5"
                                            >
                                                <span class="{{ (string) $estacao->id === $this->estacao_id ? 'border-sky-500 bg-sky-500 text-white' : 'border-zinc-300 bg-white dark:border-white/20' }} flex size-5 shrink-0 items-center justify-center rounded-full border transition-colors">
                                                    @if ((string) $estacao->id === $this->estacao_id)
                                                        <flux:icon.check class="size-3.5" />
                                                    @endif
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $estacao->site_id }}</span>
                                                    <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->municipio ?: $estacao->endereco_id }}</span>
                                                </span>
                                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
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

                        <flux:error name="estacao_id" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Ordem de Serviço --}}
            <section id="ordem-servico" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 60ms">
                <x-ui.form-section
                    icon="clipboard-document-check"
                    :title="__('Ordem de Serviço')"
                    :description="__('Vincule uma OS existente ou deixe em branco para criar automaticamente')"
                >
                    <flux:field>
                        <flux:label>{{ __('Ordem de Serviço') }}</flux:label>
                        <flux:select wire:model="ordem_servico_id">
                            <flux:select.option value="">{{ __('Criar automaticamente') }}</flux:select.option>
                            @foreach ($this->ordensDisponiveis as $ordem)
                                <flux:select.option :value="$ordem->id">{{ $ordem->codigo }} · {{ $ordem->titulo }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="ordem_servico_id" />
                    </flux:field>

                    @if ($this->ordem_servico_id)
                        @php
                            $ordemSelecionada = $this->ordensDisponiveis->firstWhere('id', (int) $this->ordem_servico_id);
                        @endphp
                        @if ($ordemSelecionada)
                            <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/60 px-4 py-3 dark:border-sky-400/20 dark:bg-sky-400/10">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                    <flux:icon.clipboard-document-list class="size-4.5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $ordemSelecionada->codigo }}</p>
                                    <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordemSelecionada->titulo }}</p>
                                </div>
                                <span class="ml-auto shrink-0 rounded-full bg-zinc-500/10 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:bg-zinc-400/10 dark:text-zinc-300">
                                    {{ $ordemSelecionada->status }}
                                </span>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center gap-3 rounded-xl border border-dashed border-emerald-200 bg-emerald-50/40 px-4 py-3 dark:border-emerald-400/20 dark:bg-emerald-400/5">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                                <flux:icon.sparkles class="size-4.5" />
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ __('Uma ordem de serviço será criada automaticamente e vinculada à estação selecionada.') }}
                            </p>
                        </div>
                    @endif
                </x-ui.form-section>
            </section>

            {{-- Ordens de Serviço (FAM) --}}
            <section id="fam" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="clipboard-document-list"
                    :title="__('Ordens de Serviço (FAM)')"
                    :description="__('OC e códigos das OS de implantação')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('OC') }}</flux:label>
                            <flux:input wire:model="oc" type="text" icon="hashtag" :placeholder="__('Ordem complexa')" />
                            <flux:error name="oc" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('OS FAM Entrega') }}</flux:label>
                            <flux:input wire:model="os_fam_entrega" type="text" :placeholder="__('Ex.: 123-456-789')" />
                            <flux:error name="os_fam_entrega" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('OS FAM Instalação') }}</flux:label>
                            <flux:input wire:model="os_fam_instalacao" type="text" :placeholder="__('Ex.: 123-456-789')" />
                            <flux:error name="os_fam_instalacao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('OS FAM Panorâmica') }}</flux:label>
                            <flux:input wire:model="os_fam_panoramica" type="text" :placeholder="__('Ex.: 123-456-789')" />
                            <flux:error name="os_fam_panoramica" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('OS FAM Desinstalação') }}</flux:label>
                            <flux:input wire:model="os_fam_desinstalacao" type="text" :placeholder="__('Ex.: 123-456-789')" />
                            <flux:error name="os_fam_desinstalacao" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Baseline, planejada e real de cada etapa')"
                >
                    @php
                        $etapasCronograma = [
                            ['key' => 'mos', 'label' => 'MOS', 'icon' => 'server', 'color' => 'bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400'],
                            ['key' => 'instalacao', 'label' => 'Instalação', 'icon' => 'wrench-screwdriver', 'color' => 'bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400'],
                            ['key' => 'integracao', 'label' => 'Integração', 'icon' => 'arrow-path', 'color' => 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400'],
                            ['key' => 'rfa', 'label' => 'RFA', 'icon' => 'flag', 'color' => 'bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400'],
                        ];
                        $tiposData = ['Baseline' => 'baseline', 'Planejada' => 'planejada', 'Real' => 'real'];
                    @endphp

                    <div class="flex flex-col gap-4">
                        @foreach ($etapasCronograma as $etapa)
                            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                <div class="mb-4 flex items-center gap-2.5">
                                    <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg {{ $etapa['color'] }}">
                                        <flux:icon :icon="$etapa['icon']" class="size-4" />
                                    </span>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $etapa['label'] }}</p>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-3">
                                    @foreach ($tiposData as $rotulo => $tipo)
                                        <flux:field>
                                            <flux:label class="text-xs">{{ __($rotulo) }}</flux:label>
                                            <flux:input wire:model="{{ $tipo . '_' . $etapa['key'] }}" type="date" size="sm" />
                                            <flux:error name="{{ $tipo . '_' . $etapa['key'] }}" />
                                        </flux:field>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                        $categoriasAnexos = [
                            ['key' => 'anexos_tssr', 'label' => __('TSSR'), 'description' => __('Termo de Serviço / Site Registration'), 'icon' => 'document-text', 'color' => 'bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400'],
                            ['key' => 'anexos_docd', 'label' => __('DOC-D'), 'description' => __('Documento de Design'), 'icon' => 'pencil-square', 'color' => 'bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400'],
                            ['key' => 'anexos_notas_fiscais', 'label' => __('Notas Fiscais'), 'description' => __('Faturas e notas emitidas'), 'icon' => 'receipt-percent', 'color' => 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400'],
                        ];
                    @endphp

                    <div class="flex flex-col gap-4">
                        @foreach ($categoriasAnexos as $categoria)
<div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 transition-colors hover:border-sky-200 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-sky-400/30">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg {{ $categoria['color'] }}">
                                        <flux:icon :icon="$categoria['icon']" class="size-4.5" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $categoria['label'] }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $categoria['description'] }}</p>
                                    </div>
                                </div>

                                <flux:input wire:model="{{ $categoria['key'] }}" type="file" multiple />
                                <flux:error name="{{ $categoria['key'] }}.*" />

                                @if (count($this->{$categoria['key']}) > 0)
                                    <p class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.check class="size-3.5 text-emerald-500" />
                                        {{ count($this->{$categoria['key']}) }} {{ __('arquivo(s) selecionado(s)') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Situação --}}
            <section id="situacao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 200ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Situação')"
                    :description="__('Ative ou desative o projeto')"
                >
                    <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-zinc-50/60 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                                <flux:icon.power class="size-4.5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Projeto ativo') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Projetos ativos aparecem no painel e podem receber novas OS.') }}</p>
                            </div>
                        </div>
                        <flux:switch wire:model="ativo" :label="__('Ativo')" />
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up sticky bottom-4 z-20 flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 240ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('tim.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">{{ __('Salvar Projeto') }}</span>
                        <span wire:loading wire:target="save">{{ __('Salvando...') }}</span>
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['estacao', 'signal', __('Estação')],
            ['ordem-servico', 'clipboard-document-check', __('Ordem de Serviço')],
            ['fam', 'clipboard-document-list', __('Ordens de Serviço (FAM)')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['anexos', 'paper-clip', __('Anexos')],
            ['situacao', 'flag', __('Situação')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O código do projeto é gerado automaticamente. Preencha as datas de baseline para iniciar o cronograma das etapas.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>