<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Projeto TIM Implantação RF')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->projeto->codigo"
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

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Informações básicas do projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Código') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="codigo" type="text" required autofocus />
                            <flux:error name="codigo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Status') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="status">
                                @foreach (\App\Models\TimProjeto::STATUS as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="nome" type="text" required />
                        <flux:error name="nome" />
                    </flux:field>

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
                        <flux:label>{{ __('Descrição') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="3" placeholder="{{ __('Opcional') }}" />
                        <flux:error name="descricao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Datas previstas para o projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Data de início') }}</flux:label>
                            <flux:input wire:model="data_inicio" type="date" />
                            <flux:error name="data_inicio" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de fim') }}</flux:label>
                            <flux:input wire:model="data_fim" type="date" />
                            <flux:error name="data_fim" />
                        </flux:field>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-2.5 border-b border-zinc-200 p-4 dark:border-white/10">
                            <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                <flux:icon.calendar-days class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Datas das etapas') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Baseline, planejada e real de cada etapa') }}</p>
                            </div>
                        </div>

                        @php
                            $etapasCronograma = [
                                'MOS' => ['baseline' => 'baseline_mos', 'planejada' => 'planejada_mos', 'real' => 'real_mos'],
                                'Instalação' => ['baseline' => 'baseline_instalacao', 'planejada' => 'planejada_instalacao', 'real' => 'real_instalacao'],
                                'Integração' => ['baseline' => 'baseline_integracao', 'planejada' => 'planejada_integracao', 'real' => 'real_integracao'],
                                'RFA' => ['baseline' => 'baseline_rfa', 'planejada' => 'planejada_rfa', 'real' => 'real_rfa'],
                            ];
                            $tiposData = ['Baseline' => 'baseline', 'Planejada' => 'planejada', 'Real' => 'real'];
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-zinc-200 bg-zinc-50/50 dark:border-white/10 dark:bg-white/[0.02]">
                                        <th class="w-32 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('Data') }}</th>
                                        @foreach ($etapasCronograma as $etapa => $campos)
                                            <th class="px-3 py-3 font-semibold text-zinc-900 dark:text-white">{{ $etapa }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiposData as $rotulo => $tipo)
                                        <tr class="border-b border-zinc-100 last:border-0 dark:border-white/5">
                                            <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">{{ __($rotulo) }}</td>
                                            @foreach ($etapasCronograma as $campos)
                                                <td class="px-3 py-3">
                                                    <flux:input wire:model="{{ $campos[$tipo] }}" type="date" size="sm" />
                                                    <flux:error name="{{ $campos[$tipo] }}" />
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Status --}}
            <section id="status" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Situação')"
                    :description="__('Ative ou desative o projeto')"
                >
                    <flux:switch wire:model="ativo" :label="__('Projeto ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 120ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('tim.show', $this->projeto) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Projeto') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['status', 'flag', __('Situação')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O código do projeto deve ser único no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>