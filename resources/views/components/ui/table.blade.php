@props([
    'total' => 0,
    'firstItem' => null,
    'lastItem' => null,
    'loadingTargets' => null,
    'hasFilters' => false,
    'delay' => '300ms',
    'minWidth' => '56rem',
    'selectedLabel' => null,
])

<div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: {{ $delay }}">
    @if ($selectedLabel)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 bg-sky-50/70 px-5 py-3 dark:border-white/10 dark:bg-sky-400/10">
            <p class="flex items-center gap-2 text-sm font-medium text-sky-800 dark:text-sky-200">
                <flux:icon.check-circle class="size-4 text-sky-500 dark:text-sky-400" />
                {{ $selectedLabel }}
            </p>

            <div class="flex flex-wrap items-center gap-2">
                {{ $selectedActions }}
                <flux:button wire:click="limparSelecao" size="sm" variant="ghost" icon="x-mark" :aria-label="__('Limpar seleção')">
                    {{ __('Limpar') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                @if ($total > 0)
                    {{ __('Mostrando') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $firstItem }}-{{ $lastItem }}</span>
                    {{ __('de') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $total }}</span>
                @else
                    {{ __('Nenhum resultado') }}
                @endif
            </p>

            <div class="flex items-center gap-3">
                @if ($loadingTargets)
                    <div wire:loading.delay wire:target="{{ $loadingTargets }}" class="flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                        <svg class="size-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        {{ __('Carregando...') }}
                    </div>
                @endif

                @if ($hasFilters)
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="inline-flex cursor-pointer items-center gap-1.5 text-sm font-medium text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                    >
                        <flux:icon.arrow-path class="size-3.5" />
                        {{ __('Limpar filtros') }}
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div wire:loading.class="opacity-40" wire:target="{{ $loadingTargets }}" class="transition-opacity duration-200">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse" style="min-width: {{ $minWidth }}">
                @if (isset($header) && $header->isNotEmpty())
                    <thead>
                        <tr wire:key="table-header" class="border-b border-zinc-100 bg-zinc-50/60 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:bg-white/[0.02] dark:text-zinc-500">
                            {{ $header }}
                        </tr>
                    </thead>
                @endif

                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>

    {{ $footer ?? '' }}
</div>