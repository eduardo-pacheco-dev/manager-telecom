@props(['radioLinkParaExcluir' => null, 'radioLinkAlvo' => null])

<flux:modal wire:model="radioLinkParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir radio link?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O radio link :nome será removido permanentemente.', ['nome' => $radioLinkAlvo?->codigo ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('radioLinkParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($radioLinkAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $radioLinkAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>