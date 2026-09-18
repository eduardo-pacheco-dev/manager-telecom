<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Editar Estação') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Atualize os dados de') }} {{ $this->estacao->site_id }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-4xl space-y-6">
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Identificação') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Site ID') }}</flux:label>
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
                        @foreach (\App\Models\Estacao::TECNOLOGIAS as $tecnologia)
                            <flux:select.option :value="$tecnologia">{{ $tecnologia }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="tecnologia" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Tipo de conexão') }}</flux:label>
                    <flux:select wire:model="tipo_conexao">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach (\App\Models\Estacao::TIPOS_CONEXAO as $conexao)
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
                        @foreach (\App\Models\Estacao::STATUS as $status)
                            <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Datas') }}</flux:heading>

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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Contratos e infraestrutura') }}</flux:heading>

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
                        @foreach (\App\Models\Estacao::DETENTORES as $detentor)
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
                        @foreach (\App\Models\Estacao::TIPOS_INFRA as $infra)
                            <flux:select.option :value="$infra">{{ $infra }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="tipo_infra" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Tipo de EV') }}</flux:label>
                    <flux:select wire:model="tipo_ev">
                        <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                        @foreach (\App\Models\Estacao::TIPOS_EV as $ev)
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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Endereço') }}</flux:heading>

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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Estrutura e localização') }}</flux:heading>

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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Informações adicionais') }}</flux:heading>

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
        </flux:card>

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('estacoes.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>