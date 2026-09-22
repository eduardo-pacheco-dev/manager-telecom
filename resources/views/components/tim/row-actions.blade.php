@props(['projeto'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $projeto->codigo" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('tim.show', $projeto)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('tim.edit', $projeto)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('projetoParaExcluir', {{ $projeto->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>