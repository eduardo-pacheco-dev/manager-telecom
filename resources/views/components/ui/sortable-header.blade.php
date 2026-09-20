@props(['field', 'label', 'align' => 'left', 'class' => null, 'sortField', 'sortDirection'])

@php
    $alinhamento = $align === 'right' ? 'justify-end text-right' : 'text-left';
@endphp

<button
    type="button"
    wire:click="sortBy('{{ $field }}')"
    class="group/col {{ $class }} {{ $alinhamento }} inline-flex cursor-pointer items-center gap-1 whitespace-nowrap transition-colors hover:text-zinc-700 dark:hover:text-zinc-200"
>
    {{ $label }}
    @if ($sortField === $field)
        <flux:icon :icon="$sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'" variant="micro" class="shrink-0 text-sky-500 dark:text-sky-400" />
    @else
        <flux:icon.chevron-up-down variant="micro" class="shrink-0 opacity-0 transition-opacity group-hover/col:opacity-60" />
    @endif
</button>