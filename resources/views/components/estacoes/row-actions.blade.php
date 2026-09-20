@props(['estacao'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $estacao->site_id" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('estacoes.show', $estacao)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('estacoes.edit', $estacao)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('estacaoParaExcluir', {{ $estacao->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>