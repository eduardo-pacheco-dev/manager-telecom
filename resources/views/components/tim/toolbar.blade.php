@props(['search', 'filtroStatus'])

<div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 260ms">
    <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :placeholder="__('Buscar por código, nome, descrição ou cliente...')"
            icon="magnifying-glass"
        />

        <flux:select wire:model.live="filtroStatus" class="w-full sm:w-44">
            <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
            @foreach (\App\Models\TimProjeto::STATUS as $status)
                <flux:select.option :value="$status">{{ $status }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Quick filter chips --}}
    <div class="mt-3 flex flex-wrap items-center gap-1.5 border-t border-zinc-100 pt-3 dark:border-white/5">
        <span class="mr-1 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Status') }}:</span>
        @foreach (['', ...\App\Models\TimProjeto::STATUS] as $chipStatus)
            <button
                type="button"
                wire:click="$set('filtroStatus', '{{ $chipStatus }}')"
                class="inline-flex cursor-pointer items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium transition-colors
                    {{ $filtroStatus === $chipStatus
                        ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/30'
                        : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:hover:bg-white/20' }}"
            >
                {{ $chipStatus === '' ? __('Todos') : $chipStatus }}
            </button>
        @endforeach
    </div>
</div>