@props(['icon' => null, 'title', 'actions' => null, 'delay' => null])

<section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" @if ($delay) style="animation-delay: {{ $delay }}" @endif>
    <header class="mb-6 flex items-center gap-3">
        @if ($icon)
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon :icon="$icon" class="size-4.5" />
            </div>
        @endif
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
        @if ($actions)
            <div class="ms-auto flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </header>

    {{ $slot }}
</section>