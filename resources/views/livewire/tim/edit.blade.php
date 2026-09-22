<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Projeto TIM Implantação RF')"
        :subtitle="__('Atualize os dados do projeto') . ' ' . $this->projeto->codigo"
        :badge="$this->projeto->codigo"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM Implantação RF'), 'href' => route('tim.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.show', $this->projeto) }}" wire:navigate variant="ghost" icon="arrow-left">
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
                    :description="__('Nome do projeto, situação e cliente')"
                >
                    <flux:field>
                        <flux:label>{{ __('Status') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:radio.group variant="cards" wire:model="status" class="flex-wrap">
                            <flux:radio variant="cards" value="Planejamento" icon="clipboard-document-list" label="{{ __('Planejamento') }}" description="{{ __('Projeto em fase de definição') }}" />
                            <flux:radio variant="cards" value="Em andamento" icon="arrow-path" label="{{ __('Em andamento') }}" description="{{ __('Execução das etapas em curso') }}" />
                            <flux:radio variant="cards" value="Pausado" icon="pause" label="{{ __('Pausado') }}" description="{{ __('Execução temporariamente suspensa') }}" />
                            <flux:radio variant="cards" value="Concluído" icon="check-circle" label="{{ __('Concluído') }}" description="{{ __('Todas as etapas finalizadas') }}" />
                            <flux:radio variant="cards" value="Cancelado" icon="x-circle" label="{{ __('Cancelado') }}" description="{{ __('Projeto não será executado') }}" />
                        </flux:radio.group>
                        <flux:error name="status" />
                    </flux:field>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Código Personalizado') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="nome" type="text" required autofocus icon="tag" :placeholder="__('Ex.: Implantação RAN TIM')" />
                            <flux:error name="nome" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Cliente') }}</flux:label>
                            <flux:select wire:model="cliente_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($clientes as $cliente)
                                    <flux:select.option :value="$cliente->id">{{ $cliente->nome }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="cliente_id" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Descrição') }}</flux:label>
                            <flux:textarea wire:model="descricao" rows="3" :placeholder="__('Descreva o objetivo e o escopo do projeto...')" />
                            <flux:error name="descricao" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
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

            {{-- Situação --}}
            <section id="situacao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
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
            <div class="animate-fade-in-up sticky bottom-4 z-20 flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 120ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('tim.show', $this->projeto) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
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
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['situacao', 'flag', __('Situação')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('As datas de baseline iniciam o cronograma. Use o status para acompanhar o andamento do projeto.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>