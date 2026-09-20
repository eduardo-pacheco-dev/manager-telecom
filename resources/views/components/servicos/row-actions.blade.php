@props(['servico'])

<flux:dropdown>
    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" :aria-label="__('Ações de') . ' ' . $servico->nome" />

    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.item :href="route('servicos.show', $servico)" icon="eye" wire:navigate>
                {{ __('Ver detalhes') }}
            </flux:menu.item>

            <flux:menu.item :href="route('servicos.edit', $servico)" icon="pencil-square" wire:navigate>
                {{ __('Editar') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item
                as="button"
                type="button"
                wire:click="toggleAtivo({{ $servico->id }})"
                :icon="$servico->ativo ? 'x-mark' : 'check'"
            >
                {{ $servico->ativo ? __('Desativar') : __('Ativar') }}
            </flux:menu.item>

            <flux:menu.item
                as="button"
                type="button"
                wire:click="$set('servicoParaExcluir', {{ $servico->id }})"
                icon="trash"
                variant="danger"
            >
                {{ __('Excluir') }}
            </flux:menu.item>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>