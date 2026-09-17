<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Editar Colaborador') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Atualize os dados de') }} {{ $this->colaborador->nome }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-2xl space-y-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="nome" :label="__('Nome completo')" type="text" required autofocus />
            <flux:input wire:model="email" :label="__('Email')" type="email" required />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="cpf" :label="__('CPF')" type="text" placeholder="000.000.000-00" required />
            <flux:input wire:model="telefone" :label="__('Telefone')" type="text" placeholder="(00) 00000-0000" />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="cargo" :label="__('Cargo')" type="text" />
            <flux:input wire:model="departamento" :label="__('Departamento')" type="text" />
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="data_admissao" :label="__('Data de admissão')" type="date" />
            <flux:input wire:model="salario" :label="__('Salário')" type="number" step="0.01" min="0" />
        </div>

        <flux:input wire:model="endereco" :label="__('Endereço')" type="text" />

        <div class="grid gap-6 sm:grid-cols-3">
            <flux:input wire:model="cidade" :label="__('Cidade')" type="text" />
            <flux:input wire:model="estado" :label="__('UF')" type="text" maxlength="2" placeholder="SP" />
            <flux:input wire:model="cep" :label="__('CEP')" type="text" placeholder="00000-000" />
        </div>

        <flux:textarea wire:model="observacoes" :label="__('Observações')" rows="3" />

        <flux:switch wire:model="ativo" :label="__('Colaborador ativo')" />

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('colaboradores.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>
