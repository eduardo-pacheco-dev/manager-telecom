<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Editar Produto') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Atualize os dados de') }} {{ $this->produto->nome }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="w-full max-w-3xl space-y-6">
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Dados do produto') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Nome do produto') }}</flux:label>
                    <flux:input wire:model="nome" type="text" required autofocus />
                    <flux:error name="nome" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Código / SKU') }}</flux:label>
                    <flux:input wire:model="codigo" type="text" placeholder="PROD-001" maxlength="50" />
                    <flux:error name="codigo" />
                </flux:field>
            </div>

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

            <flux:field>
                <flux:label>{{ __('Descrição') }}</flux:label>
                <flux:textarea wire:model="descricao" rows="3" />
                <flux:error name="descricao" />
            </flux:field>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Informações comerciais') }}</flux:heading>

            <flux:field>
                <flux:label>{{ __('Preço') }}</flux:label>
                <flux:input wire:model="preco" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                <flux:error name="preco" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Observações') }}</flux:label>
                <flux:textarea wire:model="observacoes" rows="3" />
                <flux:error name="observacoes" />
            </flux:field>

            <flux:switch wire:model="ativo" :label="__('Produto ativo')" />
        </flux:card>

        <div class="flex items-center gap-4 pt-2">
            <flux:button variant="primary" type="submit" icon="check">{{ __('Salvar') }}</flux:button>
            <flux:button href="{{ route('produtos.index') }}" wire:navigate variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>