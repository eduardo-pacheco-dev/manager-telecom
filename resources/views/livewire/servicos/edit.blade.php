<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Serviço')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->servico->nome"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Serviços'), 'href' => route('servicos.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('servicos.show', $this->servico) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            @php
                $categoriasDisponiveis = $categorias;
                if ($this->servico->categoria && ! in_array($this->servico->categoria, $categoriasDisponiveis, true)) {
                    $categoriasDisponiveis[] = $this->servico->categoria;
                }
            @endphp

            {{-- Dados do serviço --}}
            <section id="dados" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="wrench-screwdriver"
                    :title="__('Dados do serviço')"
                    :description="__('Informações de identificação do serviço')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Nome do serviço') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="nome" type="text" required autofocus />
                            <flux:error name="nome" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Código / SKU') }}</flux:label>
                            <flux:input wire:model="codigo" type="text" placeholder="SRV-001" maxlength="50" />
                            <flux:error name="codigo" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Categoria') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:select wire:model="categoria" required>
                            <flux:select.option value="">{{ __('Selecione uma categoria') }}</flux:select.option>
                            @foreach ($categoriasDisponiveis as $categoria)
                                <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="categoria" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Descrição') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="3" />
                        <flux:error name="descricao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Informações comerciais --}}
            <section id="comercial" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 60ms">
                <x-ui.form-section
                    icon="banknotes"
                    :title="__('Informações comerciais')"
                    :description="__('Tipo de cobrança, preço e observações do serviço')"
                >
                    <flux:field>
                        <flux:label>{{ __('Tipo de cobrança') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:radio.group variant="cards" wire:model.live="tipo_valor" class="flex-wrap">
                            <flux:radio variant="cards" value="servico" icon="receipt-percent" label="{{ __('Por serviço') }}" description="{{ __('Valor fixo por execução do serviço') }}" />
                            <flux:radio variant="cards" value="hora" icon="clock" label="{{ __('Por hora') }}" description="{{ __('Cobrança baseada no tempo de execução') }}" />
                        </flux:radio.group>
                        <flux:error name="tipo_valor" />
                    </flux:field>

                    @if ($tipo_valor === 'servico')
                        <flux:field>
                            <flux:label>{{ __('Preço por serviço') }}</flux:label>
                            <flux:input wire:model="preco" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="preco" />
                        </flux:field>
                    @else
                        <div class="grid gap-6 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Preço médio por hora') }}</flux:label>
                                <flux:input wire:model="preco_medio" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                                <flux:error name="preco_medio" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Tempo médio de execução (horas)') }}</flux:label>
                                <flux:input wire:model="tempo_medio_horas" type="number" step="0.25" min="0" max="168" inputmode="decimal" placeholder="0,00" />
                                <flux:error name="tempo_medio_horas" />
                            </flux:field>
                        </div>
                    @endif

                    <flux:field>
                        <flux:label>{{ __('Observações') }}</flux:label>
                        <flux:textarea wire:model="observacoes" rows="3" />
                        <flux:error name="observacoes" />
                    </flux:field>

                    <flux:switch wire:model="ativo" :label="__('Serviço ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 120ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('servicos.show', $this->servico) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Serviço') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['dados', 'wrench-screwdriver', __('Dados do serviço')],
            ['comercial', 'banknotes', __('Informações comerciais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O código do serviço deve ser único no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>