<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Editar Radio Link') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Atualize os dados de') }} {{ $this->radioLink->codigo }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-4xl space-y-6">
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Identificação') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Código') }}</flux:label>
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
                    <flux:label>{{ __('Estação A') }}</flux:label>
                    <flux:select wire:model="estacao_a_id">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach ($estacoes as $estacao)
                            <flux:select.option :value="$estacao->id">{{ $estacao->site_id }} · {{ $estacao->municipio ?: '—' }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="estacao_a_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Estação B') }}</flux:label>
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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Configuração do enlace') }}</flux:heading>

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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Informações adicionais') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Observação') }}</flux:label>
                <flux:textarea wire:model="observacao" rows="3" />
                <flux:error name="observacao" />
            </flux:field>
        </flux:card>

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('radio-links.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>