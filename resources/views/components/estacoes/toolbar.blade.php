@props(['search', 'filtroTipoElemento', 'filtroStatus', 'tiposElemento', 'statuses'])

<div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
        <div class="lg:min-w-64 lg:flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por Site ID, elemento, município ou Endereço ID...')"
                icon="magnifying-glass"
                class="w-full"
            />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
            <flux:select wire:model.live="filtroTipoElemento" class="w-full sm:w-44">
                <flux:select.option value="">{{ __('Todos os elementos') }}</flux:select.option>
                @foreach ($tiposElemento as $tipoElemento)
                    <flux:select.option :value="$tipoElemento">{{ $tipoElemento }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-40">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                @foreach ($statuses as $status)
                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>
</div>