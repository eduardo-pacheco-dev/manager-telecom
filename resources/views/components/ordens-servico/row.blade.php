@props(['ordem'])

@php
    $avatarTints = [
        'bg-sky-500/10 text-sky-700 ring-sky-500/10 dark:text-sky-300',
        'bg-emerald-500/10 text-emerald-700 ring-emerald-500/10 dark:text-emerald-300',
        'bg-violet-500/10 text-violet-700 ring-violet-500/10 dark:text-violet-300',
        'bg-amber-500/10 text-amber-700 ring-amber-500/10 dark:text-amber-300',
        'bg-rose-500/10 text-rose-700 ring-rose-500/10 dark:text-rose-300',
        'bg-teal-500/10 text-teal-700 ring-teal-500/10 dark:text-teal-300',
    ];
    $tint = $avatarTints[$ordem->id % count($avatarTints)];

    $status = [
        'Aberta' => ['badge' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
        'Em andamento' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Aguardando' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Concluída' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Cancelada' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $statusStyle = $status[$ordem->status] ?? ['badge' => 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];

    $prioridade = [
        'Baixa' => ['badge' => 'bg-zinc-100 text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Média' => ['badge' => 'bg-sky-500/10 text-sky-700 ring-1 ring-inset ring-sky-500/20 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20', 'dot' => 'bg-sky-500 dark:bg-sky-400'],
        'Alta' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Urgente' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $prioridadeStyle = $prioridade[$ordem->prioridade] ?? $prioridade['Baixa'];

    $linkAssociado = $ordem->radioLink?->codigo ?: __('Sem link associado');
@endphp

<tr wire:key="os-{{ $ordem->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
    {{-- Seleção --}}
    <td class="whitespace-nowrap px-5 py-3.5 align-middle">
        <input
            type="checkbox"
            wire:model.live="selecionados"
            value="{{ $ordem->id }}"
            :aria-label="__('Selecionar') . ' ' . $ordem->codigo"
            class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
        />
    </td>

    {{-- Código --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
            <div class="relative flex size-9 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold shadow-sm ring-1 {{ $tint }}">
                {{ \Illuminate\Support\Str::limit($ordem->codigo, 5, '') }}
                @if ($ordem->prioridade === 'Urgente')
                    <span class="absolute -right-1 -top-1 flex size-2.5">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex size-2.5 rounded-full bg-rose-500"></span>
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">{{ $ordem->codigo }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $linkAssociado }}</p>
            </div>
        </a>
    </td>

    {{-- Título --}}
    <td class="min-w-[12rem] max-w-[24rem] px-4 py-3.5 align-middle">
        <p class="truncate text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $ordem->titulo }}</p>
        @if ($ordem->solicitante)
            <p class="mt-0.5 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->solicitante }}</p>
        @endif
    </td>

    {{-- Tipo --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($ordem->tipo)
            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                {{ $ordem->tipo }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Prioridade --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($ordem->prioridade)
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $prioridadeStyle['badge'] }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $prioridadeStyle['dot'] }}"></span>
                {{ $ordem->prioridade }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Abertura --}}
    <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
        @if ($ordem->data_abertura)
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.calendar-days class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                {{ $ordem->data_abertura->format('d/m/Y') }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Status --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($ordem->status)
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle['badge'] }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle['dot'] }}"></span>
                {{ $ordem->status }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-400 dark:bg-white/5 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Ações --}}
    <td class="whitespace-nowrap px-5 py-3.5 text-right align-middle">
        <div class="inline-flex items-center justify-end">
            <x-ordens-servico.row-actions :ordem="$ordem" />
        </div>
    </td>
</tr>