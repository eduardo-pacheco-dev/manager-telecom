@props(['ordem'])

@php
    $avatarTints = [
        'bg-sky-500/15 text-sky-700 dark:text-sky-300',
        'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300',
        'bg-violet-500/15 text-violet-700 dark:text-violet-300',
        'bg-amber-500/15 text-amber-700 dark:text-amber-300',
        'bg-rose-500/15 text-rose-700 dark:text-rose-300',
        'bg-teal-500/15 text-teal-700 dark:text-teal-300',
    ];
    $tint = $avatarTints[$ordem->id % count($avatarTints)];

    $statusStyles = [
        'Aberta' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
        'Em andamento' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
        'Aguardando' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
        'Concluída' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
        'Cancelada' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
    ];
    $statusStyle = $statusStyles[$ordem->status] ?? 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300';

    $statusDots = [
        'Aberta' => 'bg-sky-500 dark:bg-sky-400',
        'Em andamento' => 'bg-amber-500 dark:bg-amber-400',
        'Aguardando' => 'bg-zinc-400 dark:bg-zinc-500',
        'Concluída' => 'bg-emerald-500 dark:bg-emerald-400',
        'Cancelada' => 'bg-rose-500 dark:bg-rose-400',
    ];
    $statusDot = $statusDots[$ordem->status] ?? 'bg-zinc-400 dark:bg-zinc-500';

    $prioridadeStyles = [
        'Baixa' => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300',
        'Média' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
        'Alta' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
        'Urgente' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400',
    ];
    $prioridadeStyle = $prioridadeStyles[$ordem->prioridade] ?? 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';

    $prioridadeDots = [
        'Baixa' => 'bg-zinc-400 dark:bg-zinc-500',
        'Média' => 'bg-sky-500 dark:bg-sky-400',
        'Alta' => 'bg-amber-500 dark:bg-amber-400',
        'Urgente' => 'bg-rose-500 dark:bg-rose-400',
    ];
    $prioridadeDot = $prioridadeDots[$ordem->prioridade] ?? 'bg-zinc-400 dark:bg-zinc-500';

    $linkAssociado = $ordem->radioLink?->codigo ?: __('Sem link associado');
@endphp

{{-- Desktop row --}}
<tr class="group hidden border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:table-row dark:border-white/5 dark:hover:bg-white/[0.02]">
    <td class="px-5 py-4 align-middle">
        <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="flex min-w-0 items-center gap-3">
            <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                {{ \Illuminate\Support\Str::limit($ordem->codigo, 5, '') }}
                @if ($ordem->prioridade === 'Urgente')
                    <span class="absolute -right-1 -top-1 flex size-3">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $ordem->codigo }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $linkAssociado }}</p>
            </div>
        </a>
    </td>

    <td class="hidden px-4 py-4 align-middle sm:table-cell">
        <div class="min-w-0">
            <p class="truncate text-sm text-zinc-700 dark:text-zinc-300">{{ $ordem->titulo }}</p>
            @if ($ordem->solicitante)
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->solicitante }}</p>
            @endif
        </div>
    </td>

    <td class="hidden px-4 py-4 align-middle lg:table-cell">
        @if ($ordem->tipo)
            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                {{ $ordem->tipo }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    <td class="hidden px-4 py-4 align-middle md:table-cell">
        @if ($ordem->prioridade)
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $prioridadeStyle }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $prioridadeDot }}"></span>
                {{ $ordem->prioridade }}
            </span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    <td class="hidden px-4 py-4 text-right align-middle lg:table-cell">
        @if ($ordem->data_abertura)
            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->data_abertura->format('d/m/Y') }}</span>
        @else
            <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
        @endif
    </td>

    <td class="px-4 py-4 align-middle">
        @if ($ordem->status)
            <span class="inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $statusDot }}"></span>
                <span class="truncate">{{ $ordem->status }}</span>
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-400 dark:bg-white/5 dark:text-zinc-500">—</span>
        @endif
    </td>

    <td class="px-5 py-4 text-right align-middle">
        <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
            <flux:button
                href="{{ route('ordens-servico.show', $ordem) }}"
                wire:navigate
                size="sm"
                variant="ghost"
                icon="eye"
                :title="__('Ver detalhes')"
                :aria-label="__('Ver detalhes de') . ' ' . $ordem->codigo"
            />
            <flux:button
                href="{{ route('ordens-servico.edit', $ordem) }}"
                wire:navigate
                size="sm"
                variant="ghost"
                icon="pencil-square"
                :title="__('Editar')"
                :aria-label="__('Editar') . ' ' . $ordem->codigo"
            />
            <flux:button
                wire:click="destroy({{ $ordem->id }})"
                size="sm"
                variant="ghost"
                icon="trash"
                :title="__('Excluir')"
                :aria-label="__('Excluir') . ' ' . $ordem->codigo"
            />
        </div>
    </td>
</tr>

{{-- Mobile card --}}
<div class="group border-b border-zinc-100 p-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 md:hidden dark:border-white/5 dark:hover:bg-white/[0.02]">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('ordens-servico.show', $ordem) }}" wire:navigate class="flex min-w-0 items-center gap-3">
            <div class="relative flex size-10 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm ring-1 ring-black/5 {{ $tint }}">
                {{ \Illuminate\Support\Str::limit($ordem->codigo, 5, '') }}
                @if ($ordem->prioridade === 'Urgente')
                    <span class="absolute -right-1 -top-1 flex size-3">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex size-3 rounded-full bg-rose-500"></span>
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-600 dark:text-white dark:group-hover:text-sky-400">{{ $ordem->codigo }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $ordem->titulo }}</p>
            </div>
        </a>

        @if ($ordem->status)
            <span class="inline-flex max-w-full shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyle }}">
                <span class="size-1.5 shrink-0 rounded-full {{ $statusDot }}"></span>
                <span class="truncate">{{ $ordem->status }}</span>
            </span>
        @endif
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
        @if ($ordem->radioLink)
            <span class="inline-flex items-center gap-1.5">
                <flux:icon.radio class="size-3.5 shrink-0 text-sky-500 dark:text-sky-400" />
                <span>{{ $ordem->radioLink->codigo }}</span>
            </span>
        @endif
        @if ($ordem->tipo)
            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-medium text-zinc-600 dark:bg-white/10 dark:text-zinc-300">
                {{ $ordem->tipo }}
            </span>
        @endif
        @if ($ordem->prioridade)
            <span class="inline-flex items-center gap-1.5">
                <span class="size-1.5 rounded-full {{ $prioridadeDot }}"></span>
                <span class="font-medium {{ str_contains($prioridadeStyle, 'text-rose') ? 'text-rose-600 dark:text-rose-400' : (str_contains($prioridadeStyle, 'text-amber') ? 'text-amber-600 dark:text-amber-400' : (str_contains($prioridadeStyle, 'text-sky') ? 'text-sky-600 dark:text-sky-400' : 'text-zinc-600 dark:text-zinc-300')) }}">
                    {{ $ordem->prioridade }}
                </span>
            </span>
        @endif
        @if ($ordem->data_abertura)
            <span class="inline-flex items-center gap-1">
                <flux:icon.calendar-days class="size-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" />
                {{ $ordem->data_abertura->format('d/m/Y') }}
            </span>
        @endif
    </div>

    <div class="mt-3 flex items-center justify-end gap-1 border-t border-zinc-100 pt-3 dark:border-white/5">
        <flux:button
            href="{{ route('ordens-servico.show', $ordem) }}"
            wire:navigate
            size="sm"
            variant="ghost"
            icon="eye"
            :title="__('Ver detalhes')"
            :aria-label="__('Ver detalhes de') . ' ' . $ordem->codigo"
        />
        <flux:button
            href="{{ route('ordens-servico.edit', $ordem) }}"
            wire:navigate
            size="sm"
            variant="ghost"
            icon="pencil-square"
            :title="__('Editar')"
            :aria-label="__('Editar') . ' ' . $ordem->codigo"
        />
        <flux:button
            wire:click="destroy({{ $ordem->id }})"
            size="sm"
            variant="ghost"
            icon="trash"
            :title="__('Excluir')"
            :aria-label="__('Excluir') . ' ' . $ordem->codigo"
        />
    </div>
</div>