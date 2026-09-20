@props(['stats', 'estacaoId'])

@php
    $fmtBytes = fn (?int $bytes): string => $bytes === null ? '—' : ($bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', '.').' MB'
        : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));
@endphp

<div class="flex flex-wrap items-center gap-x-5 gap-y-1 border-b border-zinc-100 px-4 py-2 text-xs text-zinc-500 sm:px-6 dark:border-white/5 dark:text-zinc-400">
    <span class="inline-flex items-center gap-1.5">
        <flux:icon.archive-box class="size-3.5" />
        {{ $stats['arquivos'] }} {{ __('arquivo(s)') }}
    </span>
    <span class="inline-flex items-center gap-1.5">
        <flux:icon.server class="size-3.5" />
        {{ $fmtBytes($stats['tamanho']) }}
    </span>
    @if ($estacaoId === null)
        <span class="inline-flex items-center gap-1.5">
            <flux:icon.signal class="size-3.5" />
            {{ $stats['estacoes'] }} {{ __('estação(ões)') }}
        </span>
        <span class="inline-flex items-center gap-1.5">
            <flux:icon.clipboard-document-list class="size-3.5" />
            {{ $stats['ordens'] }} {{ __('OS') }}
        </span>
    @endif
</div>