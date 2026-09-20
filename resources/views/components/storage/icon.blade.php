@props(['tipo', 'mime' => null, 'container' => 'size-10', 'icon' => 'size-5', 'rounded' => 'rounded-xl'])

@php
    $epasta = ! in_array($tipo, ['arquivo_estacao', 'arquivo_ordem', 'arquivo_radio'], true);

    if ($epasta) {
        $icone = 'folder';
        $classe = 'bg-amber-400/15 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400';
    } else {
        $icone = 'paper-clip';
        $classe = 'bg-zinc-700/15 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';

        if ($mime !== null && str_starts_with($mime, 'image/')) {
            $icone = 'photo';
            $classe = 'bg-violet-500/15 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400';
        } elseif ($mime === 'application/pdf') {
            $icone = 'document-text';
            $classe = 'bg-rose-500/15 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400';
        }
    }
@endphp

<div class="flex items-center justify-center {{ $container }} {{ $rounded }} {{ $classe }}">
    <flux:icon :icon="$icone" :class="$icon" />
</div>