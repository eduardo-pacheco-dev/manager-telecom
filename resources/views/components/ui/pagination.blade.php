@props(['paginator', 'showPerPage' => false, 'perPage' => null, 'perPageOptions' => [10, 25, 50, 100]])

@if ($paginator->hasPages())
    <div class="flex flex-col gap-3 border-t border-zinc-200 px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
        <div class="flex flex-wrap items-center gap-2">
            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Mostrando') }} {{ $paginator->firstItem() }} {{ __('a') }} {{ $paginator->lastItem() }} {{ __('de') }} {{ $paginator->total() }} {{ __('resultados') }}
            </div>

            @if ($showPerPage)
                <label class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                    <span class="whitespace-nowrap">{{ __('Itens por página') }}</span>
                    <span class="relative">
                        <select
                            wire:model.live="perPage"
                            aria-label="{{ __('Itens por página') }}"
                            class="cursor-pointer appearance-none rounded-lg border border-zinc-200 bg-white py-1 pe-7 ps-2.5 text-xs font-medium text-zinc-700 shadow-xs transition-colors hover:border-zinc-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-white/10 dark:text-zinc-300 dark:hover:border-white/20 dark:[&_option]:bg-zinc-700 dark:[&_option]:text-white"
                        >
                            @foreach ($perPageOptions as $opcao)
                                <option value="{{ $opcao }}">{{ $opcao }}</option>
                            @endforeach
                        </select>
                        <flux:icon.chevron-down class="pointer-events-none absolute end-2 top-1/2 size-3.5 -translate-y-1/2 text-zinc-400 dark:text-zinc-500" />
                    </span>
                </label>
            @endif
        </div>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                    <flux:icon.chevron-left variant="micro" />
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage"
                    aria-label="{{ __('Página anterior') }}"
                    class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                >
                    <flux:icon.chevron-left variant="micro" />
                </button>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="flex size-8 items-center justify-center rounded-lg bg-zinc-900 text-xs font-medium text-white dark:bg-white dark:text-zinc-900">
                        {{ $page }}
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="gotoPage({{ $page }})"
                        class="flex size-8 items-center justify-center rounded-lg text-xs font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        {{ $page }}
                    </button>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage"
                    aria-label="{{ __('Próxima página') }}"
                    class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                >
                    <flux:icon.chevron-right variant="micro" />
                </button>
            @else
                <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                    <flux:icon.chevron-right variant="micro" />
                </span>
            @endif
        </div>
    </div>
@endif