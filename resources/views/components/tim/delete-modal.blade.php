@props(['projetoParaExcluir' => null, 'projetoAlvo' => null])

<flux:modal wire:model="projetoParaExcluir" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ __('Excluir projeto?') }}</flux:heading>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Esta ação não pode ser desfeita. O projeto :nome será removido e suas ordens de serviço deixarão de estar vinculadas.', ['nome' => $projetoAlvo?->codigo ?? '']) }}
            </p>
        </div>

        <div class="flex gap-3">
            <flux:button variant="ghost" wire:click="$set('projetoParaExcluir', null)" class="w-full">
                {{ __('Cancelar') }}
            </flux:button>

            @if ($projetoAlvo)
                <flux:button variant="danger" type="button" wire:click="destroy({{ $projetoAlvo->id }})" class="w-full">
                    {{ __('Excluir') }}
                </flux:button>
            @endif
        </div>
    </div>
</flux:modal>