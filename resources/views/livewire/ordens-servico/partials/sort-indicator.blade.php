@if ($sortField === $field)
    @if ($sortDirection === 'asc')
        <flux:icon.chevron-up variant="micro" class="shrink-0 text-sky-500 dark:text-sky-400" />
    @else
        <flux:icon.chevron-down variant="micro" class="shrink-0 text-sky-500 dark:text-sky-400" />
    @endif
@else
    <flux:icon.chevron-up-down variant="micro" class="shrink-0 opacity-0 transition-opacity group-hover/col:opacity-60" />
@endif