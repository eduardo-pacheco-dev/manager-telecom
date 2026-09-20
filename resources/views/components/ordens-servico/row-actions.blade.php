@props(['ordem'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $ordem->codigo" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('ordens-servico.show', $ordem)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('ordens-servico.edit', $ordem)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('ordemParaExcluir', {{ $ordem->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>