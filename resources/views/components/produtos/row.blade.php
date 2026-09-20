@props(['produto'])

@php
    $avatarTints = [
        'bg-sky-500/10 text-sky-700 ring-sky-500/10 dark:text-sky-300',
        'bg-emerald-500/10 text-emerald-700 ring-emerald-500/10 dark:text-emerald-300',
        'bg-violet-500/10 text-violet-700 ring-violet-500/10 dark:text-violet-300',
        'bg-amber-500/10 text-amber-700 ring-amber-500/10 dark:text-amber-300',
        'bg-rose-500/10 text-rose-700 ring-rose-500/10 dark:text-rose-300',
        'bg-teal-500/10 text-teal-700 ring-teal-500/10 dark:text-teal-300',
    ];
    $tint = $avatarTints[$produto->id % count($avatarTints)];

    $categoriaStyle = [
        'Equipamento' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20',
        'Infraestrutura' => 'bg-violet-500/10 text-violet-700 ring-1 ring-inset ring-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/20',
        'Acessório' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20',
        'Cabeamento' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20',
    ];
    $categoriaStyle = $categoriaStyle[$produto->categoria] ?? 'bg-zinc-100 text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10';
@endphp

<tr wire:key="produto-{{ $produto->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
    {{-- Seleção --}}
    <td class="whitespace-nowrap px-5 py-3.5 align-middle">
        <input
            type="checkbox"
            wire:model.live="selecionados"
            value="{{ $produto->id }}"
            :aria-label="__('Selecionar') . ' ' . $produto->nome"
            class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
        />
    </td>

    {{-- Nome + código --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <a href="{{ route('produtos.show', $produto) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg text-xs font-semibold shadow-sm ring-1 {{ $tint }}">
                {{ \Illuminate\Support\Str::initials($produto->nome, true) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">{{ $produto->nome }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $produto->codigo ?: '—' }}</p>
            </div>
        </a>
    </td>

    {{-- Categoria + descrição --}}
    <td class="min-w-[10rem] px-4 py-3.5 align-middle">
        <div class="flex flex-col items-start gap-1">
            @if ($produto->categoria)
                <span class="inline-flex max-w-full items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $categoriaStyle }}">
                    <span class="truncate">{{ $produto->categoria }}</span>
                </span>
            @endif
            @if ($produto->descricao)
                <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $produto->descricao }}</span>
            @endif
        </div>
    </td>

    {{-- Preço --}}
    <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
        @if ($produto->preco !== null)
            <span class="inline-flex items-center gap-1.5 text-xs font-medium tabular-nums text-zinc-700 dark:text-zinc-300">
                <flux:icon.banknotes class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                R$ {{ number_format($produto->preco, 2, ',', '.') }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Status --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($produto->ativo)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">
                <span class="size-1.5 rounded-full bg-current"></span>
                {{ __('Ativo') }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20">
                <span class="size-1.5 rounded-full bg-current"></span>
                {{ __('Inativo') }}
            </span>
        @endif
    </td>

    {{-- Ações --}}
    <td class="whitespace-nowrap px-5 py-3.5 text-right align-middle">
        <div class="inline-flex items-center justify-end">
            <x-produtos.row-actions :produto="$produto" />
        </div>
    </td>
</tr>