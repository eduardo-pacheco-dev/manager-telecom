@props(['search', 'filtroRole', 'filtroStatus'])

<div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
    <div class="flex flex-col gap-2 lg:flex-row lg:items-center">
        <div class="lg:min-w-64 lg:flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por nome ou e-mail...')"
                icon="magnifying-glass"
                class="w-full"
            />
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
            <flux:select wire:model.live="filtroRole" class="w-full sm:w-44">
                <flux:select.option value="">{{ __('Todos os perfis') }}</flux:select.option>
                <flux:select.option value="admin">{{ __('Administrador') }}</flux:select.option>
                <flux:select.option value="user">{{ __('Usuário') }}</flux:select.option>
            </flux:select>

            <flux:select wire:model.live="filtroStatus" class="w-full sm:w-40">
                <flux:select.option value="">{{ __('Todos os status') }}</flux:select.option>
                <flux:select.option value="ativo">{{ __('Ativo') }}</flux:select.option>
                <flux:select.option value="inativo">{{ __('Inativo') }}</flux:select.option>
            </flux:select>
        </div>
    </div>
</div>