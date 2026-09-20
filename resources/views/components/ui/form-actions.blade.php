@props(['backRoute', 'submitLabel' => null])

<div class="sticky bottom-4 z-20 flex items-center justify-between gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90">
    <flux:button :href="$backRoute" wire:navigate variant="ghost" icon="arrow-left">
        {{ __('Cancelar') }}
    </flux:button>

    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
        {{ $submitLabel ?? __('Salvar') }}
    </flux:button>
</div>