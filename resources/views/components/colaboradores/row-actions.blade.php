@props(['colaborador'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $colaborador->nome" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('colaboradores.show', $colaborador)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('colaboradores.edit', $colaborador)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="toggleAtivo({{ $colaborador->id }})"
                :icon="$colaborador->ativo ? 'x-mark' : 'check'"
            >
                {{ $colaborador->ativo ? __('Desativar') : __('Ativar') }}
            </flux:menu.item>

            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('colaboradorParaExcluir', {{ $colaborador->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>