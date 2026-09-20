@props(['search', 'filtroDepartamento', 'filtroCategoria', 'filtroStatus', 'departamentos', 'categorias'])

<div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
        <div class="lg:min-w-64 lg:flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por nome, email, cargo ou CPF...')"
                icon="magnifying-glass"
                class="w-full"
            />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
            <flux:select wire:model.live="filtroDepartamento" class="w-full sm:w-48">
                <flux:select.option value="">{{ __('Todos os departamentos') }}</flux:select.option>
                @foreach ($departamentos as $departamento)
                    <flux:select.option :value="$departamento">{{ $departamento }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroCategoria" class="w-full sm:w-40">
                <flux:select.option value="">{{ __('Todas as categorias') }}</flux:select.option>
                @foreach ($categorias as $categoria)
                    <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-36">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                <flux:select.option value="ativo">{{ __('Ativos') }}</flux:select.option>
                <flux:select.option value="inativo">{{ __('Inativos') }}</flux:select.option>
            </flux:select>
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex shrink-0 items-center">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>