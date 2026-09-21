<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Estação')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->estacao->site_id"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Estações'), 'href' => route('estacoes.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('estacoes.show', $this->estacao) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            @php
                foreach (['tecnologia' => 'tecnologias', 'tipo_conexao' => 'tiposConexao', 'endereco_id' => 'enderecos', 'station_id' => 'stations', 'status' => 'statuses', 'detentor_area' => 'detentores', 'tipo_infra' => 'tiposInfra', 'tipo_ev' => 'tiposEv'] as $campo => $lista) {
                    $valorAtual = $this->estacao->{$campo};
                    if ($valorAtual && ! in_array($valorAtual, ${$lista}, true)) {
                        ${$lista}[] = $valorAtual;
                    }
                }
            @endphp

            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Informações básicas de identificação da estação')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Site ID') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="site_id" type="text" required autofocus />
                            <flux:error name="site_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Tipo de elemento') }}</flux:label>
                            <flux:select wire:model="tipo_elemento">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::TIPOS_ELEMENTO as $tipo)
                                    <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_elemento" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Tecnologia') }}</flux:label>
                            <flux:select wire:model="tecnologia">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($tecnologias as $tecnologia)
                                    <flux:select.option :value="$tecnologia">{{ $tecnologia }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tecnologia" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Tipo de conexão') }}</flux:label>
                            <flux:select wire:model="tipo_conexao">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($tiposConexao as $conexao)
                                    <flux:select.option :value="$conexao">{{ $conexao }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_conexao" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Endereço ID') }}</flux:label>
                            <flux:input wire:model="endereco_id" type="text" />
                            <flux:error name="endereco_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Classificação') }}</flux:label>
                            <flux:select wire:model="classificacao">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::CLASSIFICACOES as $classificacao)
                                    <flux:select.option :value="$classificacao">{{ $classificacao }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="classificacao" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Station ID') }}</flux:label>
                            <flux:input wire:model="station_id" type="text" />
                            <flux:error name="station_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Status') }}</flux:label>
                            <flux:select wire:model="status">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($statuses as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Datas --}}
            <section id="datas" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Datas')"
                    :description="__('Ciclo de vida da estação')"
                >
                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Data de aquisição') }}</flux:label>
                            <flux:input wire:model="data_aquisicao" type="date" />
                            <flux:error name="data_aquisicao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de construção') }}</flux:label>
                            <flux:input wire:model="data_construcao" type="date" />
                            <flux:error name="data_construcao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de ativação') }}</flux:label>
                            <flux:input wire:model="data_ativacao" type="date" />
                            <flux:error name="data_ativacao" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Data de desativação') }}</flux:label>
                            <flux:input wire:model="data_desativacao" type="date" />
                            <flux:error name="data_desativacao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Data de cancelamento') }}</flux:label>
                            <flux:input wire:model="data_cancelamento" type="date" />
                            <flux:error name="data_cancelamento" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Contratos e infraestrutura --}}
            <section id="contratos" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="document-check"
                    :title="__('Contratos e infraestrutura')"
                    :description="__('Informações de contratos e infraestrutura')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Tipo de contrato da Área') }}</flux:label>
                            <flux:select wire:model="tipo_contrato_area">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::TIPOS_CONTRATO as $contrato)
                                    <flux:select.option :value="$contrato">{{ $contrato }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_contrato_area" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Detentor da Área') }}</flux:label>
                            <flux:select wire:model="detentor_area">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($detentores as $detentor)
                                    <flux:select.option :value="$detentor">{{ $detentor }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="detentor_area" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Tipo de contrato Infra') }}</flux:label>
                            <flux:select wire:model="tipo_contrato_infra">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::TIPOS_CONTRATO as $contrato)
                                    <flux:select.option :value="$contrato">{{ $contrato }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_contrato_infra" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Detentor de Infra') }}</flux:label>
                            <flux:select wire:model="detentor_infra">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::DETENTORES as $detentor)
                                    <flux:select.option :value="$detentor">{{ $detentor }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="detentor_infra" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Tipo de Infra') }}</flux:label>
                            <flux:select wire:model="tipo_infra">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($tiposInfra as $infra)
                                    <flux:select.option :value="$infra">{{ $infra }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_infra" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Tipo de EV') }}</flux:label>
                            <flux:select wire:model="tipo_ev">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($tiposEv as $ev)
                                    <flux:select.option :value="$ev">{{ $ev }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_ev" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Fornecedor de EV') }}</flux:label>
                            <flux:select wire:model="fornecedor_ev">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::FORNECEDORES_EV as $fornecedor)
                                    <flux:select.option :value="$fornecedor">{{ $fornecedor }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="fornecedor_ev" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Endereço --}}
            <section id="endereco" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="map-pin"
                    :title="__('Endereço')"
                    :description="__('Localização do endereço da estação')"
                >
                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Tipo de logradouro') }}</flux:label>
                            <flux:select wire:model="tipo_logradouro">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::TIPOS_LOGRADOURO as $logradouroTipo)
                                    <flux:select.option :value="$logradouroTipo">{{ $logradouroTipo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo_logradouro" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Logradouro') }}</flux:label>
                            <flux:input wire:model="logradouro" type="text" />
                            <flux:error name="logradouro" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Número') }}</flux:label>
                            <flux:input wire:model="numero" type="text" placeholder="S/N" />
                            <flux:error name="numero" />
                        </flux:field>

                        <flux:field class="sm:col-span-2">
                            <flux:label>{{ __('Complemento') }}</flux:label>
                            <flux:input wire:model="complemento" type="text" />
                            <flux:error name="complemento" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Bairro') }}</flux:label>
                            <flux:input wire:model="bairro" type="text" />
                            <flux:error name="bairro" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Município') }}</flux:label>
                            <flux:input wire:model="municipio" type="text" />
                            <flux:error name="municipio" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-4">
                        <flux:field>
                            <flux:label>{{ __('UF') }}</flux:label>
                            <flux:input wire:model="estado" type="text" maxlength="2" placeholder="AC" />
                            <flux:error name="estado" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('CEP') }}</flux:label>
                            <flux:input wire:model="cep" type="text" placeholder="00000-000" maxlength="9" inputmode="numeric" />
                            <flux:error name="cep" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Regional') }}</flux:label>
                            <flux:select wire:model="regional">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::REGIONAIS as $regional)
                                    <flux:select.option :value="$regional">{{ $regional }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="regional" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Tipo da torre') }}</flux:label>
                            <flux:input wire:model="tipo_torre" type="text" />
                            <flux:error name="tipo_torre" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Estrutura e localização --}}
            <section id="estrutura" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 160ms">
                <x-ui.form-section
                    icon="building-office-2"
                    :title="__('Estrutura e localização')"
                    :description="__('Coordenadas e medidas da estrutura')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Latitude') }}</flux:label>
                            <flux:input wire:model="latitude" type="number" step="0.000001" inputmode="decimal" placeholder="-10,925094" />
                            <flux:error name="latitude" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Longitude') }}</flux:label>
                            <flux:input wire:model="longitude" type="number" step="0.000001" inputmode="decimal" placeholder="-69,554056" />
                            <flux:error name="longitude" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('AEV Nominal') }}</flux:label>
                            <flux:input wire:model="aev_nominal" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="aev_nominal" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Área de solo (m²)') }}</flux:label>
                            <flux:input wire:model="area_solo" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="area_solo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Altura da estrutura (m)') }}</flux:label>
                            <flux:input wire:model="altura_estrutura" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="altura_estrutura" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Ordem Complexa') }}</flux:label>
                        <flux:input wire:model="ordem_complexa" type="text" />
                        <flux:error name="ordem_complexa" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Informações adicionais --}}
            <section id="informacoes" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 200ms">
                <x-ui.form-section
                    icon="chat-bubble-left-right"
                    :title="__('Informações adicionais')"
                    :description="__('Observações e situação da estação')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Observação') }}</flux:label>
                            <flux:textarea wire:model="observacao" rows="3" />
                            <flux:error name="observacao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Justificativa') }}</flux:label>
                            <flux:textarea wire:model="justificativa" rows="3" />
                            <flux:error name="justificativa" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <flux:field>
                            <flux:label>{{ __('Situação') }}</flux:label>
                            <flux:select wire:model="situacao">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\Estacao::SITUACOES as $situacao)
                                    <flux:select.option :value="$situacao">{{ $situacao }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="situacao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Observação THQ') }}</flux:label>
                            <flux:textarea wire:model="observacao_thq" rows="1" />
                            <flux:error name="observacao_thq" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('OTs') }}</flux:label>
                            <flux:select wire:model="ots">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                <flux:select.option value="Sim">{{ __('Sim') }}</flux:select.option>
                                <flux:select.option value="Não">{{ __('Não') }}</flux:select.option>
                            </flux:select>
                            <flux:error name="ots" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 240ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('estacoes.show', $this->estacao) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Estação') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['datas', 'calendar-days', __('Datas')],
            ['contratos', 'document-check', __('Contratos e infraestrutura')],
            ['endereco', 'map-pin', __('Endereço')],
            ['estrutura', 'building-office-2', __('Estrutura e localização')],
            ['informacoes', 'chat-bubble-left-right', __('Informações adicionais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O Site ID deve ser único no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>