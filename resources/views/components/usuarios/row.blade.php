@props(['usuario'])

@php
    $tints = [
        'bg-sky-500/10 text-sky-700 ring-sky-500/10 dark:text-sky-300',
        'bg-emerald-500/10 text-emerald-700 ring-emerald-500/10 dark:text-emerald-300',
        'bg-violet-500/10 text-violet-700 ring-violet-500/10 dark:text-violet-300',
        'bg-amber-500/10 text-amber-700 ring-amber-500/10 dark:text-amber-300',
        'bg-rose-500/10 text-rose-700 ring-rose-500/10 dark:text-rose-300',
        'bg-teal-500/10 text-teal-700 ring-teal-500/10 dark:text-teal-300',
    ];
    $tint = $tints[$usuario->id % count($tints)];

    $roleBadge = $usuario->role === 'admin'
        ? 'bg-violet-500/10 text-violet-700 ring-1 ring-inset ring-violet-500/20 dark:bg-violet-400/10 dark:text-violet-300 dark:ring-violet-400/20'
        : 'bg-zinc-100 text-zinc-600 ring-1 ring-inset ring-zinc-200 dark:bg-white/10 dark:text-zinc-300 dark:ring-white/10';
@endphp

<tr wire:key="usuario-{{ $usuario->id }}" class="group border-b border-zinc-100 transition-colors last:border-b-0 hover:bg-zinc-50/70 dark:border-white/5 dark:hover:bg-white/[0.02]">
    {{-- Seleção --}}
    <td class="whitespace-nowrap px-5 py-3.5 align-middle">
        <input
            type="checkbox"
            wire:model.live="selecionados"
            value="{{ $usuario->id }}"
            :aria-label="__('Selecionar') . ' ' . $usuario->name"
            class="size-4 cursor-pointer rounded border-zinc-300 text-sky-600 focus:ring-sky-500 dark:border-white/15 dark:bg-white/10 dark:checked:bg-sky-500"
        />
    </td>

    {{-- Usuário --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <a href="{{ route('usuarios.show', $usuario) }}" wire:navigate class="group/link flex min-w-0 items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg text-xs font-bold shadow-sm ring-1 {{ $tint }}">
                {{ $usuario->initials() }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-zinc-900 transition-colors group-hover/link:text-sky-600 dark:text-white dark:group-hover/link:text-sky-400">
                    {{ $usuario->name }}
                    @if ($usuario->id === auth()->id())
                        <span class="ml-1 text-xs font-normal text-zinc-400 dark:text-zinc-500">({{ __('você') }})</span>
                    @endif
                </p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $usuario->email }}</p>
            </div>
        </a>
    </td>

    {{-- Perfil --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $roleBadge }}">
            {{ $usuario->role === 'admin' ? __('Administrador') : __('Usuário') }}
        </span>
    </td>

    {{-- Verificação --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($usuario->email_verified_at)
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.check-circle class="size-3.5 text-emerald-500 dark:text-emerald-400" />
                {{ $usuario->email_verified_at->format('d/m/Y') }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                <flux:icon.clock class="size-3.5" />
                {{ __('Não verificado') }}
            </span>
        @endif
    </td>

    {{-- Criado em --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $usuario->created_at?->format('d/m/Y') }}</span>
    </td>

    {{-- Status --}}
    <td class="whitespace-nowrap px-4 py-3.5 align-middle">
        @if ($usuario->ativo)
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
            <x-usuarios.row-actions :usuario="$usuario" />
        </div>
    </td>
</tr>