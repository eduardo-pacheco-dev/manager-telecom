<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Cliente')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->cliente->nome"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Clientes'), 'href' => route('clientes.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('clientes.show', $this->cliente) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Dados da empresa --}}
            <section id="dados-empresa" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Dados da empresa')"
                    :description="__('Informações de identificação do cliente')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Razão social') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="nome" type="text" required autofocus />
                            <flux:error name="nome" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Email') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="email" type="email" required />
                            <flux:error name="email" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('CNPJ') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model.live="documento" type="text" placeholder="00.000.000/0000-00" maxlength="18" inputmode="numeric" required />
                            <flux:error name="documento" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Telefone') }}</flux:label>
                            <flux:input wire:model.live="telefone" type="tel" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" />
                            <flux:error name="telefone" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Segmento') }}</flux:label>
                        <flux:select wire:model="segmento">
                            <flux:select.option value="">{{ __('Selecione um segmento') }}</flux:select.option>
                            @foreach ($segmentos as $segmento)
                                <flux:select.option :value="$segmento">{{ $segmento }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="segmento" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Endereço --}}
            <section id="endereco" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 60ms">
                <x-ui.form-section
                    icon="map-pin"
                    :title="__('Endereço')"
                    :description="__('Localização do cliente')"
                >
                    <flux:field>
                        <flux:label>{{ __('Endereço') }}</flux:label>
                        <flux:input wire:model="endereco" type="text" />
                        <flux:error name="endereco" />
                    </flux:field>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Cidade') }}</flux:label>
                            <flux:input wire:model="cidade" type="text" />
                            <flux:error name="cidade" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('UF') }}</flux:label>
                            <flux:input wire:model.live="estado" type="text" maxlength="2" placeholder="SP" />
                            <flux:error name="estado" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('CEP') }}</flux:label>
                            <flux:input wire:model.live="cep" type="text" placeholder="00000-000" maxlength="9" inputmode="numeric" />
                            <flux:error name="cep" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Informações adicionais --}}
            <section id="informacoes" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Informações adicionais')"
                    :description="__('Observações e status do cliente')"
                >
                    <flux:field>
                        <flux:label>{{ __('Observações') }}</flux:label>
                        <flux:textarea wire:model="observacoes" rows="3" />
                        <flux:error name="observacoes" />
                    </flux:field>

                    <flux:switch wire:model="ativo" :label="__('Cliente ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 180ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('clientes.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Cliente') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['dados-empresa', 'identification', __('Dados da empresa')],
            ['endereco', 'map-pin', __('Endereço')],
            ['informacoes', 'document-text', __('Informações adicionais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O CNPJ e o e-mail devem ser únicos no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>