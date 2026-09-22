@props(['projeto'])

@php
    $avatarTints = [
        'bg-sky-500/10 text-sky-700 ring-sky-500/10 dark:text-sky-300',
        'bg-violet-500/10 text-violet-700 ring-violet-500/10 dark:text-violet-300',
        'bg-emerald-500/10 text-emerald-700 ring-emerald-500/10 dark:text-emerald-300',
        'bg-amber-500/10 text-amber-700 ring-amber-500/10 dark:text-amber-300',
        'bg-rose-500/10 text-rose-700 ring-rose-500/10 dark:text-rose-300',
        'bg-teal-500/10 text-teal-700 ring-teal-500/10 dark:text-teal-300',
    ];
    $tint = $avatarTints[$projeto->id % count($avatarTints)];

    $status = [
        'Planejamento' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Em andamento' => ['badge' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
        'Pausado' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Concluído' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Cancelado' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $statusStyle = $status[$projeto->status] ?? ['badge' => 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $cliente = $projeto->cliente?->nome;
@endphp

<tr wire:key="tim-projeto-{{ $projeto->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
    {{-- Seleção --}}
    <td class="whitespace-nowrap px-5 py-3.5 align-middle">
        <input
            type="checkbox"
            wire:model.live="selecionados"
            value="{{ $projeto->id }}"
            aria-label="{{ __('Selecionar') . ' ' . $projeto->codigo }}"
            class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
        />
    </td>

    {{-- Projeto --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <a href="{{ route('tim.show', $projeto) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold shadow-sm ring-1 {{ $tint }}">
                <flux:icon.folder class="size-4.5" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">{{ $projeto->codigo }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $projeto->nome }}</p>
            </div>
        </a>
    </td>

    {{-- Status --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle['badge'] }}">
            <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle['dot'] }}"></span>
            {{ $projeto->status }}
        </span>
    </td>

    {{-- Cliente --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($cliente)
            <div class="flex items-center gap-2">
                <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                    <flux:icon.user class="size-3.5" />
                </span>
                <span class="max-w-40 truncate text-sm text-zinc-600 dark:text-zinc-300">{{ $cliente }}</span>
            </div>
        @else
            <span class="text-sm text-zinc-300 dark:text-zinc-600">—</span>
        @endif
    </td>

    {{-- OS vinculadas --}}
    <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold tabular-nums text-zinc-900 dark:text-white">
            <flux:icon.clipboard-document-list class="size-4 text-zinc-400 dark:text-zinc-500" />
            {{ $projeto->ordens_servico_count }}
        </span>
    </td>

    {{-- Início --}}
    <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
        @if ($projeto->data_inicio)
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.calendar-days class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                {{ $projeto->data_inicio->format('d/m/Y') }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Situação --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($projeto->ativo)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">
                <span class="size-1.5 shrink-0 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                {{ __('Ativo') }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-zinc-500/10 px-2.5 py-1 text-xs font-medium text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20">
                <span class="size-1.5 shrink-0 rounded-full bg-zinc-400 dark:bg-zinc-500"></span>
                {{ __('Inativo') }}
            </span>
        @endif
    </td>

    {{-- Ações --}}
    <td class="whitespace-nowrap px-5 py-3.5 text-right align-middle">
        <div class="inline-flex items-center justify-end">
            <x-tim.row-actions :projeto="$projeto" />
        </div>
    </td>
</tr>