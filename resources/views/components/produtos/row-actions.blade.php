@props(['produto'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $produto->nome" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('produtos.show', $produto)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('produtos.edit', $produto)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="toggleAtivo({{ $produto->id }})"
                :icon="$produto->ativo ? 'x-mark' : 'check'"
            >
                {{ $produto->ativo ? __('Desativar') : __('Ativar') }}
            </flux:menu.item>

            <flux:menu.item
                as="button"
                type="button"
                wire:click="destroy({{ $produto->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>