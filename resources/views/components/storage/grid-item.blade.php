@props(['item', 'fmtBytes'])

@php
    $epasta = ! in_array($item['tipo'], ['arquivo_estacao', 'arquivo_ordem', 'arquivo_radio'], true);
@endphp

<div wire:key="item-{{ $item['tipo'] }}-{{ $item['id'] }}" class="group relative">
    @if ($epasta)
        <button
            type="button"
            wire:click="{{ $item['abrir'] }}"
            class="flex w-full cursor-pointer flex-col items-center gap-2 rounded-2xl border border-zinc-200 bg-white p-4 text-center transition-all duration-150 hover:border-sky-300 hover:bg-sky-50/50 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-sky-500/50 dark:hover:bg-sky-400/5"
        >
            <x-ui.file-icon :folder="$epasta" :mime="$item['mime'] ?? null" container="size-14" icon="size-7" rounded="rounded-2xl" />
            <span class="line-clamp-2 text-xs font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</span>
            <span class="truncate text-[11px] text-zinc-400 dark:text-zinc-500">{{ $item['subtitulo'] }}</span>
        </button>
    @else
        <div class="flex w-full flex-col items-center gap-2 rounded-2xl border border-zinc-200 bg-white p-4 text-center transition-all duration-150 hover:border-zinc-300 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-white/20">
            <x-ui.file-icon :folder="$epasta" :mime="$item['mime'] ?? null" container="size-14" icon="size-7" rounded="rounded-2xl" />
            <span class="line-clamp-2 text-xs font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</span>
            <span class="truncate text-[11px] text-zinc-400 dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</span>

            <div class="absolute inset-0 flex items-center justify-center gap-2 rounded-2xl bg-zinc-900/60 opacity-0 backdrop-blur-[2px] transition-opacity duration-150 group-hover:opacity-100 dark:bg-zinc-950/70">
                <a
                    href="{{ $item['download'] }}"
                    class="flex size-9 items-center justify-center rounded-full bg-white/90 text-zinc-800 transition-transform hover:scale-110"
                    :aria-label="__('Baixar')"
                >
                    <flux:icon.arrow-down-tray class="size-4.5" />
                </a>
                <flux:button
                    wire:click="{{ $item['remover'] }}"
                    wire:confirm="{{ __('Remover este anexo?') }}"
                    variant="danger"
                    icon="trash"
                    size="sm"
                    :aria-label="__('Remover')"
                    class="size-9 rounded-full"
                />
            </div>
        </div>
    @endif
</div>