@php
    $icon = $anexo->mime !== null && str_starts_with($anexo->mime, 'image/')
        ? 'photo'
        : ($anexo->mime === 'application/pdf' ? 'document-text' : 'paper-clip');

    $color = $anexo->mime !== null && str_starts_with($anexo->mime, 'image/')
        ? 'bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400'
        : ($anexo->mime === 'application/pdf'
            ? 'bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400'
            : 'bg-zinc-700/10 text-zinc-600 dark:bg-white/10 dark:text-zinc-300');

    $fmtBytes = fn (?int $bytes): string => $bytes === null ? '—' : ($bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', '.').' MB'
        : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));
@endphp

<div class="flex items-center gap-3 border-t border-zinc-100 py-3 first:border-t-0 dark:border-white/5">
    <div class="{{ $color }} flex size-9 shrink-0 items-center justify-center rounded-lg">
        <flux:icon :icon="$icon" class="size-4.5" />
    </div>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $anexo->nome }}</p>
        <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
            {{ $fmtBytes($anexo->tamanho) }} · {{ $anexo->created_at?->format('d/m/Y') }}
        </p>
    </div>
    <a
        href="{{ $downloadRoute }}"
        class="inline-flex items-center rounded-lg px-2 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
        aria-label="{{ __('Baixar') }}"
    >
        <flux:icon.arrow-down-tray class="size-4" />
    </a>
    <flux:button
        wire:click="{{ $deleteMethod }}"
        wire:confirm="{{ __('Remover este anexo?') }}"
        variant="ghost"
        icon="trash"
        size="sm"
        :aria-label="__('Remover anexo')"
    />
</div>