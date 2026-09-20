@props(['icon' => 'folder', 'title', 'description' => null])

<div class="flex flex-col items-center justify-center px-6 py-20 text-center">
    <div class="flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
        <flux:icon :icon="$icon" class="size-8 text-zinc-400 dark:text-zinc-500" />
    </div>
    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>