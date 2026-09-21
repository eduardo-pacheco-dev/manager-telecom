<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Novo Projeto Nokia')"
        :subtitle="__('Cadastre um novo projeto de implantação Nokia')"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('Nokia'), 'href' => route('nokia.index')],
            ['label' => __('Novo'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('nokia.index') }}" wire:navigate variant="ghost" icon="arrow-left">
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
                            <div class="flex items-center gap-2">
                                <flux:input wire:model="codigo" type="text" readonly required class="flex-1 cursor-default bg-zinc-50 text-zinc-600 dark:bg-white/5 dark:text-zinc-300" />
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                                    <flux:icon.sparkles class="size-3.5" />
                                    {{ __('Automático') }}
                                </span>
                            </div>
                            <flux:error name="codigo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Status') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="status">
                                @foreach (\App\Models\NokiaProjeto::STATUS as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="nome" type="text" required autofocus placeholder="{{ __('Ex.: Implantação RAN TIM') }}" />
                        <flux:error name="nome" />
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
                    <flux:button href="{{ route('nokia.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
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
                    {{ __('Após criar o projeto, você poderá vincular ordens de serviço na página de detalhes.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>