@props(['ordemParaExcluir' => null, 'ordemAlvo' => null])

<flux:modal wire:model="ordemParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir ordem de serviço?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. A ordem :nome será removida permanentemente.', ['nome' => $ordemAlvo?->codigo ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('ordemParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($ordemAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $ordemAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>