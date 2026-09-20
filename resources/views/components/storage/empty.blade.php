@props(['search', 'estacaoId'])

<div class="flex flex-col items-center justify-center px-6 py-20 text-center">
    <div class="flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
        <flux:icon.folder class="size-8 text-zinc-400 dark:text-zinc-500" />
    </div>
    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
        {{ $search !== '' ? __('Nenhum resultado encontrado') : __('Pasta vazia') }}
    </p>
    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
        @if ($search !== '')
            {{ __('Tente ajustar sua busca para encontrar o que procura.') }}
        @elseif ($estacaoId !== null)
            {{ __('Envie arquivos para esta estação ou navegue pelas ordens de serviço.') }}
        @else
            {{ __('As estações aparecem aqui como pastas. Navegue para acessar os arquivos.') }}
        @endif
    </p>
    @if ($search !== '')
        <flux:button wire:click="$set('search', '')" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
            {{ __('Limpar busca') }}
        </flux:button>
    @elseif ($estacaoId !== null)
        <flux:button wire:click="abrirUpload" variant="primary" size="sm" icon="plus" class="mt-5">
            {{ __('Enviar arquivo') }}
        </flux:button>
    @endif
</div>