@props(['servicoParaExcluir' => null, 'servicoAlvo' => null])

<flux:modal wire:model="servicoParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir serviço?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O serviço :nome será removido permanentemente.', ['nome' => $servicoAlvo?->nome ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('servicoParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($servicoAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $servicoAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>