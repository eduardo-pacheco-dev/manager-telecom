@props(['sections' => []])
{{-- sections: array of [id, icon, label] --}}

<aside
    class="sticky top-4 hidden animate-fade-in-up lg:block"
    x-data="{
        active: '{{ $sections[0]['id'] ?? '' }}',
        sections: @js(array_column($sections, 'id')),
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) this.active = entry.target.id;
                });
            }, { rootMargin: '-25% 0px -65% 0px' });
            this.sections.forEach((id) => {
                const el = document.getElementById(id);
                if (el) observer.observe(el);
            });
        },
        go(id) {
            document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
    }"
>
    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
        <div class="border-b border-zinc-200 px-4 py-3.5 dark:border-white/10">
            <p class="flex items-center gap-2 text-sm font-medium text-zinc-900 dark:text-white">
                <flux:icon.queue-list class="size-4 text-sky-500 dark:text-sky-400" />
                {{ __('Seções do formulário') }}
            </p>
        </div>

        <nav class="p-2">
            @foreach ($sections as [$id, $icon, $label])
                <button
                    type="button"
                    @click="go('{{ $id }}')"
                    class="group flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm transition-colors"
                    :class="active === '{{ $id }}'
                        ? 'bg-sky-500/10 font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300'
                        : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-white'"
                >
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-md transition-colors"
                        :class="active === '{{ $id }}'
                            ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/30'
                            : 'bg-zinc-100 text-zinc-400 group-hover:bg-zinc-200 dark:bg-white/10 dark:text-zinc-500 dark:group-hover:bg-white/20'"
                    >
                        <flux:icon :icon="$icon" class="size-3.5" />
                    </span>
                    <span class="truncate">{{ $label }}</span>
                </button>
            @endforeach
        </nav>
    </div>

    @if ($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</aside>