@props(['radioLink'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $radioLink->codigo" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('radio-links.show', $radioLink)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('radio-links.edit', $radioLink)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('radioLinkParaExcluir', {{ $radioLink->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>