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

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('OC') }}</flux:label>
                            <flux:input wire:model="oc" type="text" :placeholder="__('Ordem complexa')" />
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
                    </div>

                    <flux:field>

                        <div x-data="{ open: false }" class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between gap-2 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-left text-sm shadow-sm transition-colors hover:bg-zinc-50 dark:border-white/15 dark:bg-white/5 dark:hover:bg-white/10"
                                :class="open ? 'ring-2 ring-accent ring-offset-2' : ''"
                            >
                                @if ($this->estacaoSelecionada)
                                    <span class="flex min-w-0 items-center gap-2">
                                        <flux:icon.map-pin class="size-4 shrink-0 text-sky-500 dark:text-sky-400" />
                                        <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $this->estacaoSelecionada->site_id }}</span>
                                        <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->estacaoSelecionada->municipio ?: $this->estacaoSelecionada->endereco_id }}</span>
                                    </span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">{{ __('Selecione uma estação...') }}</span>
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
                                class="absolute z-30 mt-1.5 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl shadow-black/5 dark:border-white/10 dark:bg-zinc-900"
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
                    {{ __('Vincule as estações deste projeto. Cada estação pertence a apenas um projeto.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>