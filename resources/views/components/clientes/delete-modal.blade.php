@props(['clienteParaExcluir', 'clienteAlvo'])

<flux:modal wire:model="clienteParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir cliente?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O cliente :nome será removido permanentemente.', ['nome' => $clienteAlvo?->nome ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('clienteParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($clienteAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $clienteAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>