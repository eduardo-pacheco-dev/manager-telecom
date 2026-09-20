@props([
    'label',
    'value',
    'icon',
    'color' => 'zinc',
    'progress' => null,
    'footnote' => null,
    'delay' => '40ms',
])

@php
    $colors = [
        'zinc' => [
            'icon' => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-200',
            'bar' => 'bg-gradient-to-r from-zinc-400 to-zinc-600 dark:from-zinc-500 dark:to-zinc-300',
            'track' => 'bg-zinc-100 dark:bg-white/10',
            'glow' => 'bg-zinc-200/40 dark:bg-white/5',
            'value' => 'text-zinc-900 dark:text-white',
        ],
        'sky' => [
            'icon' => 'bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400',
            'bar' => 'bg-gradient-to-r from-sky-500 to-sky-400',
            'track' => 'bg-sky-500/10 dark:bg-sky-400/10',
            'glow' => 'bg-sky-200/40 dark:bg-sky-400/10',
            'value' => 'text-sky-600 dark:text-sky-400',
        ],
        'emerald' => [
            'icon' => 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400',
            'bar' => 'bg-gradient-to-r from-emerald-500 to-emerald-400',
            'track' => 'bg-emerald-500/10 dark:bg-emerald-400/10',
            'glow' => 'bg-emerald-200/40 dark:bg-emerald-400/10',
            'value' => 'text-emerald-600 dark:text-emerald-400',
        ],
        'rose' => [
            'icon' => 'bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400',
            'bar' => 'bg-gradient-to-r from-rose-500 to-rose-400',
            'track' => 'bg-rose-500/10 dark:bg-rose-400/10',
            'glow' => 'bg-rose-200/40 dark:bg-rose-400/10',
            'value' => 'text-rose-600 dark:text-rose-400',
        ],
    ];

    $paleta = $colors[$color] ?? $colors['zinc'];
@endphp

<div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: {{ $delay }}">
    <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full blur-2xl {{ $paleta['glow'] }}"></div>
    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-1.5 text-3xl font-semibold tracking-tight {{ $paleta['value'] }}">{{ $value }}</p>
        </div>
        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $paleta['icon'] }} transition-transform group-hover:scale-105">
            <flux:icon :icon="$icon" class="size-5" />
        </div>
    </div>
    @if ($progress !== null || $footnote)
        <div class="relative mt-4">
            @if ($progress !== null)
                <div class="h-1.5 overflow-hidden rounded-full {{ $paleta['track'] }}">
                    <div class="h-full rounded-full {{ $paleta['bar'] }} transition-all duration-700" style="width: {{ min(100, (int) $progress) }}%"></div>
                </div>
            @endif
            @if ($footnote)
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $footnote }}</p>
            @endif
        </div>
    @endif
</div>