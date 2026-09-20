@props(['breadcrumbs', 'view', 'showArvore', 'estacaoId'])

<div class="sticky top-0 z-20 border-b border-zinc-200 bg-white/95 backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/95">
    <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:gap-3 sm:px-6">
        {{-- Breadcrumb --}}
        <nav class="flex min-w-0 items-center gap-1 overflow-x-auto" aria-label="{{ __('Navegação') }}">
            <flux:button
                wire:click="voltarRaiz"
                variant="ghost"
                size="sm"
                icon="arrow-left"
                :aria-label="__('Voltar')"
                class="shrink-0"
            />
            @foreach ($breadcrumbs as $index => $crumb)
                @if ($index > 0)
                    <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
                @endif
                @if ($crumb['acao'])
                    <button
                        type="button"
                        wire:click="{{ $crumb['acao'] }}"
                        class="shrink-0 cursor-pointer rounded-lg px-2 py-1 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        {{ $crumb['label'] }}
                    </button>
                @else
                    <span class="shrink-0 px-2 py-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <div class="relative flex-1 sm:flex-none">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('Buscar...')"
                    icon="magnifying-glass"
                    class="w-full sm:w-56"
                />
            </div>

            <flux:select wire:model.live="sortField" class="hidden w-36 md:block">
                <flux:select.option value="nome">{{ __('Nome') }}</flux:select.option>
                <flux:select.option value="data">{{ __('Modificado') }}</flux:select.option>
                <flux:select.option value="tamanho">{{ __('Tamanho') }}</flux:select.option>
            </flux:select>

            <flux:button
                wire:click="alternarArvore"
                variant="ghost"
                size="sm"
                icon="folder-open"
                :title="__('Mostrar/ocultar árvore')"
                :aria-label="__('Mostrar/ocultar árvore')"
                :class="$showArvore ? 'text-sky-600 dark:text-sky-400' : ''"
            />

            <flux:button
                wire:click="alternarView"
                variant="ghost"
                size="sm"
                :icon="$view === 'lista' ? 'squares-2x2' : 'list-bullet'"
                :title="$view === 'lista' ? __('Exibir em grade') : __('Exibir em lista')"
                :aria-label="$view === 'lista' ? __('Exibir em grade') : __('Exibir em lista')"
            />

            @if ($estacaoId !== null)
                <flux:button wire:click="abrirUpload" variant="primary" icon="plus">
                    {{ __('Novo') }}
                </flux:button>
            @endif
        </div>
    </div>
</div>