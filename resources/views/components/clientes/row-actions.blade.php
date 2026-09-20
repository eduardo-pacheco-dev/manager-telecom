@props(['cliente'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $cliente->nome" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('clientes.show', $cliente)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('clientes.edit', $cliente)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="toggleAtivo({{ $cliente->id }})"
                :icon="$cliente->ativo ? 'x-mark' : 'check'"
            >
                {{ $cliente->ativo ? __('Desativar') : __('Ativar') }}
            </flux:menu.item>

            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('clienteParaExcluir', {{ $cliente->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>