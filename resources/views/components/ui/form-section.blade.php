@props(['icon' => null, 'title', 'description' => null])

<flux:card class="space-y-5">
    <header class="flex items-start gap-3">
        @if ($icon)
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon :icon="$icon" class="size-4.5" />
            </div>
        @endif
        <div class="min-w-0">
            <flux:heading size="lg">{{ $title }}</flux:heading>
            @if ($description)
                <flux:text class="mt-1">{{ $description }}</flux:text>
            @endif
        </div>
    </header>

    {{ $slot }}
</flux:card>