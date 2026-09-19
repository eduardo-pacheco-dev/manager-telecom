<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Nova Ordem de Serviço') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Preencha os dados para cadastrar uma nova ordem') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-4xl space-y-6">
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Identificação') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Código') }}</flux:label>
                    <flux:input wire:model="codigo" type="text" required autofocus placeholder="OS-0001" />
                    <flux:error name="codigo" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Tipo') }}</flux:label>
                    <flux:select wire:model="tipo">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach (\App\Models\OrdemServico::TIPOS as $tipo)
                            <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="tipo" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Título') }}</flux:label>
                <flux:input wire:model="titulo" type="text" required />
                <flux:error name="titulo" />
            </flux:field>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Enlace e status') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Radio Link') }}</flux:label>
                    <flux:select wire:model="radio_link_id">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach ($radioLinks as $radioLink)
                            <flux:select.option :value="$radioLink->id">{{ $radioLink->codigo }} · {{ $radioLink->estacaoA->site_id }} → {{ $radioLink->estacaoB->site_id }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="radio_link_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Responsável') }}</flux:label>
                    <flux:select wire:model="responsavel_id">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach ($responsaveis as $responsavel)
                            <flux:select.option :value="$responsavel->id">{{ $responsavel->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="responsavel_id" />
                </flux:field>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Estação A') }}</flux:label>
                    <flux:input wire:model="estacao_a_id" type="text" disabled placeholder="{{ __('Preenchido automaticamente') }}" />
                    <flux:error name="estacao_a_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Estação B') }}</flux:label>
                    <flux:input wire:model="estacao_b_id" type="text" disabled placeholder="{{ __('Preenchido automaticamente') }}" />
                    <flux:error name="estacao_b_id" />
                </flux:field>
            </div>

            <div class="grid gap-6 sm:grid-cols-3">
                <flux:field>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <flux:select wire:model="status">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach (\App\Models\OrdemServico::STATUS as $status)
                            <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Prioridade') }}</flux:label>
                    <flux:select wire:model="prioridade">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach (\App\Models\OrdemServico::PRIORIDADES as $prioridade)
                            <flux:select.option :value="$prioridade">{{ $prioridade }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="prioridade" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Solicitante') }}</flux:label>
                    <flux:input wire:model="solicitante" type="text" placeholder="{{ __('Ex.: NOC') }}" />
                    <flux:error name="solicitante" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Cronograma') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-3">
                <flux:field>
                    <flux:label>{{ __('Data de abertura') }}</flux:label>
                    <flux:input wire:model="data_abertura" type="date" />
                    <flux:error name="data_abertura" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Data de agendamento') }}</flux:label>
                    <flux:input wire:model="data_agendamento" type="date" />
                    <flux:error name="data_agendamento" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Data de conclusão') }}</flux:label>
                    <flux:input wire:model="data_conclusao" type="date" />
                    <flux:error name="data_conclusao" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Descrição') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Descrição do serviço') }}</flux:label>
                <flux:textarea wire:model="descricao" rows="4" />
                <flux:error name="descricao" />
            </flux:field>
        </flux:card>

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>