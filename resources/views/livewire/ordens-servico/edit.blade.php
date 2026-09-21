<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    @php
        $linkSelecionado = $radioLinks->firstWhere('id', (int) $radio_link_id);
        $estacaoSelecionada = $estacoes->firstWhere('id', (int) $estacao_a_id);
    @endphp

    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Ordem de Serviço')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->ordemServico->codigo"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Ordens de Serviço'), 'href' => route('ordens-servico.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('ordens-servico.show', $this->ordemServico) }}" wire:navigate variant="ghost" icon="arrow-left">
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
                    :description="__('Dados básicos da ordem de serviço')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Código') }} <span class="text-rose-500">*</span></flux:label>
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

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Código personalizado') }}</flux:label>
                            <flux:input wire:model="codigo_personalizado" type="text" />
                            <flux:error name="codigo_personalizado" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Código do cliente') }}</flux:label>
                            <flux:input wire:model="codigo_cliente" type="text" />
                            <flux:error name="codigo_cliente" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
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
                            <flux:label>{{ __('Ordem complexa') }}</flux:label>
                            <flux:input wire:model="ordem_complexa" type="text" />
                            <flux:error name="ordem_complexa" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Título') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="titulo" type="text" required placeholder="{{ __('Ex.: Manutenção preventiva no link principal') }}" />
                        <flux:error name="titulo" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Vínculo --}}
            <section id="enlace" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="radio"
                    :title="__('Vínculo')"
                    :description="__('Defina o escopo da ordem e os responsáveis')"
                >
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
                </x-ui.form-section>
            </section>

            {{-- Status e prioridade --}}
            <section id="status-prioridade" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Status e prioridade')"
                    :description="__('Defina a situação atual e a urgência da ordem')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
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
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Solicitante') }}</flux:label>
                        <flux:input wire:model="solicitante" type="text" placeholder="{{ __('Ex.: NOC') }}" />
                        <flux:error name="solicitante" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Datas previstas para a execução')"
                >
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
                </x-ui.form-section>
            </section>

            {{-- Descrição --}}
            <section id="descricao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 160ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Descrição')"
                    :description="__('Detalhes e instruções do serviço')"
                >
                    <flux:field>
                        <flux:label>{{ __('Descrição do serviço') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="4" />
                        <flux:error name="descricao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 200ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('ordens-servico.show', $this->ordemServico) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Ordem') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['enlace', 'radio', __('Enlace e responsáveis')],
            ['status-prioridade', 'flag', __('Status e prioridade')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['descricao', 'document-text', __('Descrição')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Ao selecionar um radio link, as estações A e B são preenchidas automaticamente. Use o status e a prioridade para priorizar o atendimento.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>