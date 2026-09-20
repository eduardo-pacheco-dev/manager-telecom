<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Radio Link')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->radioLink->codigo"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Radio Links'), 'href' => route('radio-links.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('radio-links.show', $this->radioLink) }}" wire:navigate variant="ghost" icon="arrow-left">
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
                    :description="__('Identificação do enlace e das estações conectadas')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Código') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="codigo" type="text" required autofocus placeholder="RL-0001" />
                            <flux:error name="codigo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Nome') }}</flux:label>
                            <flux:input wire:model="nome" type="text" placeholder="{{ __('Ex.: Backhaul Centro') }}" />
                            <flux:error name="nome" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Estação A') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="estacao_a_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($estacoes as $estacao)
                                    <flux:select.option :value="$estacao->id">{{ $estacao->site_id }} · {{ $estacao->municipio ?: '—' }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="estacao_a_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Estação B') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="estacao_b_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($estacoes as $estacao)
                                    <flux:select.option :value="$estacao->id">{{ $estacao->site_id }} · {{ $estacao->municipio ?: '—' }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="estacao_b_id" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\RadioLink::STATUS as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de ativação') }}</flux:label>
                            <flux:input wire:model="data_ativacao" type="date" />
                            <flux:error name="data_ativacao" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Configuração do enlace --}}
            <section id="configuracao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="signal"
                    :title="__('Configuração do enlace')"
                    :description="__('Parâmetros técnicos de transmissão')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Frequência (GHz)') }}</flux:label>
                            <flux:input wire:model="frequencia" type="number" step="0.001" min="0" inputmode="decimal" placeholder="23,000" />
                            <flux:error name="frequencia" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Capacidade') }}</flux:label>
                            <flux:select wire:model="capacidade">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                <flux:select.option value="10 Mbps">10 Mbps</flux:select.option>
                                <flux:select.option value="100 Mbps">100 Mbps</flux:select.option>
                                <flux:select.option value="1 Gbps">1 Gbps</flux:select.option>
                                <flux:select.option value="2 Gbps">2 Gbps</flux:select.option>
                                <flux:select.option value="10 Gbps">10 Gbps</flux:select.option>
                            </flux:select>
                            <flux:error name="capacidade" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Canal') }}</flux:label>
                            <flux:input wire:model="canal" type="text" placeholder="1E1" />
                            <flux:error name="canal" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Polarização') }}</flux:label>
                            <flux:select wire:model="polarizacao">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\RadioLink::POLARIZACOES as $polarizacao)
                                    <flux:select.option :value="$polarizacao">{{ $polarizacao }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="polarizacao" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Fabricante') }}</flux:label>
                            <flux:select wire:model="fabricante">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\RadioLink::FABRICANTES as $fabricante)
                                    <flux:select.option :value="$fabricante">{{ $fabricante }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="fabricante" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Modelo') }}</flux:label>
                            <flux:input wire:model="modelo" type="text" placeholder="MINI-LINK 6363" />
                            <flux:error name="modelo" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Distância (km)') }}</flux:label>
                            <flux:input wire:model="distancia" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="distancia" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Informações adicionais --}}
            <section id="informacoes" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="chat-bubble-left-right"
                    :title="__('Informações adicionais')"
                    :description="__('Observações sobre o enlace')"
                >
                    <flux:field>
                        <flux:label>{{ __('Observação') }}</flux:label>
                        <flux:textarea wire:model="observacao" rows="3" />
                        <flux:error name="observacao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 120ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('radio-links.show', $this->radioLink) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Radio Link') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['configuracao', 'signal', __('Configuração do enlace')],
            ['informacoes', 'chat-bubble-left-right', __('Informações adicionais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O código deve ser único. As estações A e B devem ser diferentes.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>