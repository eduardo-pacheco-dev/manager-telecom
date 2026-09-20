@props(['title', 'subtitle', 'badge' => null, 'breadcrumbs' => []])
{{-- breadcrumbs: array of [label, href|null] --}}

<div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-emerald-500/5 dark:from-sky-400/10 dark:via-transparent dark:to-emerald-400/10"></div>
    <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-400/15"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-10 size-56 rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-400/15"></div>

    <div class="relative flex flex-col gap-5 p-5 sm:p-6">
        @if ($breadcrumbs)
            <flux:breadcrumbs>
                @foreach ($breadcrumbs as $crumb)
                    @if ($crumb['href'])
                        <flux:breadcrumbs.item :href="$crumb['href']" wire:navigate>{{ $crumb['label'] }}</flux:breadcrumbs.item>
                    @else
                        <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ $crumb['label'] }}</flux:breadcrumbs.item>
                    @endif
                @endforeach
            </flux:breadcrumbs>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" level="1" class="flex items-center gap-3">
                    {{ $title }}
                    @isset($titleBadge)
                        {{ $titleBadge }}
                    @elseif ($badge)
                        <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-sky-500/10 px-2.5 py-0.5 text-sm font-semibold text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                            {{ $badge }}
                        </span>
                    @endif
                </flux:heading>
                @if ($subtitle)
                    <flux:subheading size="lg" class="mt-1">{{ $subtitle }}</flux:subheading>
                @endif
            </div>

            @if ($slot->isNotEmpty())
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end [&>*]:w-full sm:[&>*]:w-auto">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>