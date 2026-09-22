<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Novo Relatório TIM')"
        :subtitle="__('Crie um relatório vinculado a uma OS e a um site') . ' — ' . $this->projeto->codigo"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM'), 'href' => route('tim.index')],
            ['label' => $this->projeto->codigo, 'href' => route('tim.show', $this->projeto)],
            ['label' => __('Novo Relatório'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.show', $this->projeto) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Vínculos --}}
            <section id="vinculos" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="link"
                    :title="__('Vínculos')"
                    :description="__('Vincule o relatório a uma ordem de serviço e a uma estação')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Ordem de serviço') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="ordem_servico_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($this->ordensDisponiveis as $ordem)
                                    <flux:select.option :value="$ordem->id">{{ $ordem->codigo }} · {{ $ordem->titulo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="ordem_servico_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Site ID') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="estacao_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($this->estacoesDisponiveis as $estacao)
                                    <flux:select.option :value="$estacao->id">{{ $estacao->site_id }} · {{ $estacao->municipio ?: $estacao->endereco_id }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="estacao_id" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Datas --}}
            <section id="datas" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Datas')"
                    :description="__('Datas de início, planejada e real do relatório')"
                >
                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Data de início') }}</flux:label>
                            <flux:input wire:model="data_inicio" type="date" />
                            <flux:error name="data_inicio" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data planejada') }}</flux:label>
                            <flux:input wire:model="data_planejada" type="date" />
                            <flux:error name="data_planejada" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data real') }}</flux:label>
                            <flux:input wire:model="data_real" type="date" />
                            <flux:error name="data_real" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Status --}}
            <section id="status" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Status')"
                    :description="__('Situação atual do relatório')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Status') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="status">
                                @foreach (\App\Models\TimRelatorio::STATUS as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Observação') }}</flux:label>
                            <flux:textarea wire:model="observacao" rows="3" />
                            <flux:error name="observacao" />
                        </flux:field>
                    </div>
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
                        {{ __('Salvar Relatório') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['vinculos', 'link', __('Vínculos')],
            ['datas', 'calendar-days', __('Datas')],
            ['status', 'flag', __('Status')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O relatório acompanha as datas do serviço no site. O status pode ser atualizado na página de detalhes.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>