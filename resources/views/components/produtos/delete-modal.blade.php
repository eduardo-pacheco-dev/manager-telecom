@props(['produtoParaExcluir' => null, 'produtoAlvo' => null])

<flux:modal wire:model="produtoParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir produto?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O produto :nome será removido permanentemente.', ['nome' => $produtoAlvo?->nome ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('produtoParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($produtoAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $produtoAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>