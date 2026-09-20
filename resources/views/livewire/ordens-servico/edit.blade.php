<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    @php
        $linkSelecionado = $radioLinks->firstWhere('id', (int) $radio_link_id);
        $estacaoSelecionada = $estacoes->firstWhere('id', (int) $estacao_a_id);
    @endphp

    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Editar Ordem de Serviço') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Atualize os dados de') }} {{ $this->ordemServico->codigo }}</flux:subheading>
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
                        @if ($this->ordemServico->tipo && ! in_array($this->ordemServico->tipo, \App\Models\OrdemServico::tiposDisponiveis(), true))
                            <flux:select.option :value="$this->ordemServico->tipo" selected>{{ $this->ordemServico->tipo }} ({{ __('inativo') }})</flux:select.option>
                        @endif
                        @foreach (\App\Models\OrdemServico::tiposDisponiveis() as $tipo)
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
            <flux:heading size="lg">{{ __('Vínculo e status') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Escopo') }}</flux:label>
                <flux:radio.group variant="cards" wire:model.live="escopo" class="flex-wrap">
                    <flux:radio variant="cards" value="Enlace" icon="radio" label="{{ __('Enlace') }}" description="{{ __('Ordem vinculada a um radio link') }}" />
                    <flux:radio variant="cards" value="Estação" icon="signal" label="{{ __('Estação') }}" description="{{ __('Ordem somente relacionada a uma estação') }}" />
                    <flux:radio variant="cards" value="Outro" icon="document-text" label="{{ __('Outro') }}" description="{{ __('Sem vínculo com enlace ou estação') }}" />
                </flux:radio.group>
                <flux:error name="escopo" />
            </flux:field>

            @if ($escopo === 'Enlace')
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

                {{-- Link selecionado: visual A → B --}}
                @if ($linkSelecionado)
                    <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                        <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                <flux:icon.map-pin class="size-4.5" />
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação A') }}</p>
                                <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoA->site_id }}</p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center justify-center gap-2 px-1">
                            <flux:icon.arrow-right class="size-4 text-violet-500 dark:text-violet-400" />
                            <span class="rounded-full bg-violet-500/10 px-2.5 py-0.5 text-xs font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">{{ $linkSelecionado->codigo }}</span>
                        </div>

                        <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                <flux:icon.map-pin class="size-4.5" />
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação B') }}</p>
                                <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoB->site_id }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-4 py-3 dark:border-white/10 dark:bg-white/[0.02]">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500">
                            <flux:icon.sparkles class="size-4.5" />
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Selecione um radio link para preencher as estações A/B automaticamente.') }}
                        </p>
                    </div>
                @endif

                {{-- Estações (hidden fields) --}}
                <input type="hidden" wire:model="estacao_a_id" />
                <input type="hidden" wire:model="estacao_b_id" />
            @elseif ($escopo === 'Estação')
                <div class="grid gap-6 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Estação') }}</flux:label>
                        <flux:select wire:model="estacao_a_id">
                            <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                            @foreach ($estacoes as $estacao)
                                <flux:select.option :value="$estacao->id">{{ $estacao->site_id }} · {{ $estacao->municipio ?: $estacao->endereco_id }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="estacao_a_id" />
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

                @if ($estacaoSelecionada)
                    <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50/60 px-4 py-3 dark:border-sky-400/20 dark:bg-sky-400/10">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                            <flux:icon.map-pin class="size-4.5" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $estacaoSelecionada->site_id }}</p>
                            <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacaoSelecionada->municipio ?: __('Sem município') }}@if ($estacaoSelecionada->estado) · {{ $estacaoSelecionada->estado }}@endif</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="grid gap-6 sm:grid-cols-2">
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
            @endif

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