@props(['colaboradorParaExcluir', 'colaboradorAlvo'])

<flux:modal wire:model="colaboradorParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir colaborador?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O colaborador :nome será removido permanentemente.', ['nome' => $colaboradorAlvo?->nome ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('colaboradorParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($colaboradorAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $colaboradorAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>