@props(['usuario'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $usuario->name" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('usuarios.show', $usuario)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('usuarios.edit', $usuario)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        @if ($usuario->id !== auth()->id())
            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item
                    as="button"
                    type="button"
                    wire:click="$set('usuarioParaExcluir', {{ $usuario->id }})"
                    icon="trash"
                    variant="danger"
                >
                    {{ __('Excluir') }}
                </flux:menu.item>
            </flux:menu.radio.group>
        @endif
    </flux:menu>
</flux:dropdown>