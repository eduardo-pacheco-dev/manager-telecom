<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Novo Colaborador') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Preencha os dados para cadastrar um novo colaborador') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-3xl space-y-6">
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Dados pessoais') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Nome completo') }}</flux:label>
                    <flux:input wire:model="nome" type="text" required autofocus />
                    <flux:error name="nome" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Email') }}</flux:label>
                    <flux:input wire:model="email" type="email" required />
                    <flux:error name="email" />
                </flux:field>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('CPF') }}</flux:label>
                    <flux:input wire:model="cpf" type="text" placeholder="000.000.000-00" maxlength="14" inputmode="numeric" required />
                    <flux:error name="cpf" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Telefone') }}</flux:label>
                    <flux:input wire:model="telefone" type="tel" placeholder="(00) 00000-0000" maxlength="15" inputmode="tel" />
                    <flux:error name="telefone" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Dados profissionais') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Categoria') }}</flux:label>
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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Endereço') }}</flux:heading>

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
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Informações adicionais') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Observações') }}</flux:label>
                <flux:textarea wire:model="observacoes" rows="3" />
                <flux:error name="observacoes" />
            </flux:field>

            <flux:switch wire:model="ativo" :label="__('Colaborador ativo')" />
        </flux:card>

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('colaboradores.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>