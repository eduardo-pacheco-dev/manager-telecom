@props(['estacaoParaExcluir' => null, 'estacaoAlvo' => null])

<flux:modal wire:model="estacaoParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir estação?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. A estação :nome será removida permanentemente.', ['nome' => $estacaoAlvo?->site_id ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('estacaoParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($estacaoAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $estacaoAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>