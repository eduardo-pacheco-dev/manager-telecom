<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Colaborador')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->colaborador->nome"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Colaboradores'), 'href' => route('colaboradores.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('colaboradores.show', $this->colaborador) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Dados pessoais --}}
            <section id="dados-pessoais" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Dados pessoais')"
                    :description="__('Informações básicas de identificação do colaborador')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Nome completo') }} <span class="text-rose-500">*</span></flux:label>
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
                            <flux:label>{{ __('CPF') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="cpf" type="text" placeholder="000.000.000-00" maxlength="14" inputmode="numeric" required />
                            <flux:error name="cpf" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Telefone') }}</flux:label>
                            <flux:input wire:model="telefone" type="tel" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" />
                            <flux:error name="telefone" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Dados profissionais --}}
            <section id="dados-profissionais" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 60ms">
                <x-ui.form-section
                    icon="briefcase"
                    :title="__('Dados profissionais')"
                    :description="__('Categoria, cargo e informações de contratação')"
                >
                    <flux:field>
                        <flux:label>{{ __('Categoria') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:select wire:model="categoria" required>
                            <flux:select.option value="">{{ __('Selecione uma categoria') }}</flux:select.option>
                            @foreach ($categorias as $categoria)
                                <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="categoria" />
                    </flux:field>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Cargo') }}</flux:label>
                            <flux:input wire:model="cargo" type="text" />
                            <flux:error name="cargo" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Departamento') }}</flux:label>
                            <flux:input wire:model="departamento" type="text" />
                            <flux:error name="departamento" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Data de admissão') }}</flux:label>
                            <flux:input wire:model="data_admissao" type="date" />
                            <flux:error name="data_admissao" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Salário') }}</flux:label>
                            <flux:input wire:model="salario" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="salario" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Endereço --}}
            <section id="endereco" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="map-pin"
                    :title="__('Endereço')"
                    :description="__('Localização do colaborador')"
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
                            <flux:input wire:model="estado" type="text" maxlength="2" placeholder="SP" />
                            <flux:error name="estado" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('CEP') }}</flux:label>
                            <flux:input wire:model="cep" type="text" placeholder="00000-000" maxlength="9" inputmode="numeric" />
                            <flux:error name="cep" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Informações adicionais --}}
            <section id="informacoes" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 180ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Informações adicionais')"
                    :description="__('Observações e status do colaborador')"
                >
                    <flux:field>
                        <flux:label>{{ __('Observações') }}</flux:label>
                        <flux:textarea wire:model="observacoes" rows="3" />
                        <flux:error name="observacoes" />
                    </flux:field>

                    <flux:switch wire:model="ativo" :label="__('Colaborador ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 240ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('colaboradores.show', $this->colaborador) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Colaborador') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['dados-pessoais', 'identification', __('Dados pessoais')],
            ['dados-profissionais', 'briefcase', __('Dados profissionais')],
            ['endereco', 'map-pin', __('Endereço')],
            ['informacoes', 'document-text', __('Informações adicionais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O CPF e o e-mail devem ser únicos no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>