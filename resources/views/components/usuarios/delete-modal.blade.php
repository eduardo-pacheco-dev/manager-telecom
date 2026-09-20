@props(['usuarioParaExcluir' => null, 'usuarioAlvo' => null])

<flux:modal wire:model="usuarioParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir usuário?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O usuário :nome será removido permanentemente.', ['nome' => $usuarioAlvo?->name ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('usuarioParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($usuarioAlvo && $usuarioAlvo->id !== auth()->id())
                <flux:button variant="danger" type="button" wire:click="destroy({{ $usuarioAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>