@props(['item', 'fmtBytes', 'fmtData'])

@php
    $epasta = ! in_array($item['tipo'], ['arquivo_estacao', 'arquivo_ordem', 'arquivo_radio'], true);
@endphp

<div wire:key="item-{{ $item['tipo'] }}-{{ $item['id'] }}" class="group flex items-center gap-3 border-b border-zinc-100 px-5 py-3 transition-colors last:border-b-0 {{ $epasta ? 'hover:bg-sky-50/60 dark:hover:bg-sky-400/5' : 'hover:bg-zinc-50/80 dark:hover:bg-white/[0.02]' }}">
    @if ($epasta)
        <button
            type="button"
            wire:click="{{ $item['abrir'] }}"
            class="flex min-w-0 flex-1 cursor-pointer items-center gap-3 text-left"
        >
            <x-storage.icon :tipo="$item['tipo']" :mime="$item['mime'] ?? null" container="size-10" icon="size-5" />
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-700 dark:text-white dark:group-hover:text-sky-300">{{ $item['nome'] }}</p>
                <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ $item['subtitulo'] }}</p>
            </div>
        </button>
        <span class="hidden w-32 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtData($item['data']) }}</span>
        <span class="hidden w-24 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">
            {{ (int) $item['tamanho'] }} {{ __('item(ns)') }}
        </span>
        <div class="flex w-16 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
            <flux:button
                wire:click="{{ $item['abrir'] }}"
                size="sm"
                variant="ghost"
                icon="eye"
                :title="__('Abrir')"
                :aria-label="__('Abrir') . ' ' . $item['nome']"
            />
        </div>
    @else
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <x-storage.icon :tipo="$item['tipo']" :mime="$item['mime'] ?? null" container="size-10" icon="size-5" />
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</p>
                <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</p>
            </div>
        </div>
        <span class="hidden w-32 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtData($item['data']) }}</span>
        <span class="hidden w-24 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</span>
        <div class="flex w-16 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
            <a
                href="{{ $item['download'] }}"
                class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                :aria-label="__('Baixar') . ' ' . $item['nome']"
            >
                <flux:icon.arrow-down-tray class="size-4.5" />
            </a>
            <flux:button
                wire:click="{{ $item['remover'] }}"
                wire:confirm="{{ __('Remover este anexo?') }}"
                variant="ghost"
                icon="trash"
                size="sm"
                :aria-label="__('Remover') . ' ' . $item['nome']"
            />
        </div>
    @endif
</div>