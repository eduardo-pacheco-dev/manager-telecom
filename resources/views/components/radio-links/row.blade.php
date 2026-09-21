@props(['radioLink'])

@php
    $avatarTints = [
        'bg-sky-500/10 text-sky-700 ring-sky-500/10 dark:text-sky-300',
        'bg-emerald-500/10 text-emerald-700 ring-emerald-500/10 dark:text-emerald-300',
        'bg-violet-500/10 text-violet-700 ring-violet-500/10 dark:text-violet-300',
        'bg-amber-500/10 text-amber-700 ring-amber-500/10 dark:text-amber-300',
        'bg-rose-500/10 text-rose-700 ring-rose-500/10 dark:text-rose-300',
        'bg-teal-500/10 text-teal-700 ring-teal-500/10 dark:text-teal-300',
    ];
    $tint = $avatarTints[$radioLink->id % count($avatarTints)];

    $statusStyles = [
        'Em implantação' => ['badge' => 'bg-amber-500/10 text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20', 'dot' => 'bg-amber-500 dark:bg-amber-400'],
        'Ativo' => ['badge' => 'bg-emerald-500/10 text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20', 'dot' => 'bg-emerald-500 dark:bg-emerald-400'],
        'Inativo' => ['badge' => 'bg-zinc-500/10 text-zinc-600 ring-1 ring-inset ring-zinc-500/20 dark:bg-zinc-400/10 dark:text-zinc-300 dark:ring-zinc-400/20', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'],
        'Desativado' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
        'Cancelado' => ['badge' => 'bg-rose-500/10 text-rose-700 ring-1 ring-inset ring-rose-500/20 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/20', 'dot' => 'bg-rose-500 dark:bg-rose-400'],
    ];
    $statusStyle = $statusStyles[$radioLink->status] ?? ['badge' => 'bg-zinc-100 text-zinc-700 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10', 'dot' => 'bg-zinc-400 dark:bg-zinc-500'];
@endphp

<tr wire:key="radio-link-{{ $radioLink->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
    {{-- Seleção --}}
    <td class="whitespace-nowrap px-5 py-3.5 align-middle">
        <input
            type="checkbox"
            wire:model.live="selecionados"
            value="{{ $radioLink->id }}"
            aria-label="{{ __('Selecionar') . ' ' . $radioLink->codigo }}"
            class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
        />
    </td>

    {{-- Código + nome --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <a href="{{ route('radio-links.show', $radioLink) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg text-xs font-bold shadow-sm ring-1 {{ $tint }}">
                {{ \Illuminate\Support\Str::limit($radioLink->codigo, 5, '') }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">{{ $radioLink->codigo }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $radioLink->nome ?: '—' }}</p>
            </div>
        </a>
    </td>

    {{-- Estações A → B --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <div class="flex items-center gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
            @if ($radioLink->estacaoA)
                <a href="{{ route('estacoes.show', $radioLink->estacaoA) }}" wire:navigate class="truncate font-medium text-sky-600 hover:underline dark:text-sky-400">{{ $radioLink->estacaoA->site_id }}</a>
            @else
                <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
            @endif
            <flux:icon.arrow-right class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
            @if ($radioLink->estacaoB)
                <a href="{{ route('estacoes.show', $radioLink->estacaoB) }}" wire:navigate class="truncate font-medium text-sky-600 hover:underline dark:text-sky-400">{{ $radioLink->estacaoB->site_id }}</a>
            @else
                <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
            @endif
        </div>
    </td>

    {{-- Frequência --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($radioLink->frequencia !== null)
            <span class="text-xs tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($radioLink->frequencia, 3, ',', '.') }} GHz</span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Capacidade --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($radioLink->capacidade)
            <span class="text-xs text-zinc-600 dark:text-zinc-300">{{ $radioLink->capacidade }}</span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Fabricante --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($radioLink->fabricante)
            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                {{ $radioLink->fabricante }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Ativação --}}
    <td class="whitespace-nowrap px-4 py-3.5 text-right align-middle">
        @if ($radioLink->data_ativacao)
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.calendar-days class="size-3.5 text-zinc-400 dark:text-zinc-500" />
                {{ $radioLink->data_ativacao->format('d/m/Y') }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Status --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($radioLink->status)
            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle['badge'] }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $statusStyle['dot'] }}"></span>
                <span class="truncate">{{ $radioLink->status }}</span>
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    {{-- Ações --}}
    <td class="whitespace-nowrap px-5 py-3.5 text-right align-middle">
        <div class="inline-flex items-center justify-end">
            <x-radio-links.row-actions :radio-link="$radioLink" />
        </div>
    </td>
</tr>