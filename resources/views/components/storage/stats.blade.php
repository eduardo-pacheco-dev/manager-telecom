@props(['stats', 'estacaoId'])

@php
    $fmtBytes = fn (?int $bytes): string => $bytes === null ? '0 B' : ($bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', '.').' MB'
        : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));

    $itens = [
        [
            'icone' => 'archive-box',
            'classe' => 'text-zinc-400 dark:text-zinc-500',
            'valor' => (string) $stats['arquivos'],
            'rotulo' => __('arquivo(s)'),
        ],
        [
            'icone' => 'server',
            'classe' => 'text-zinc-400 dark:text-zinc-500',
            'valor' => $fmtBytes($stats['tamanho']),
            'rotulo' => null,
        ],
    ];

    if ($estacaoId === null) {
        $itens[] = [
            'icone' => 'signal',
            'classe' => 'text-emerald-500 dark:text-emerald-400',
            'valor' => (string) $stats['estacoes'],
            'rotulo' => __('estação(ões)'),
        ];
        $itens[] = [
            'icone' => 'clipboard-document-list',
            'classe' => 'text-violet-500 dark:text-violet-400',
            'valor' => (string) $stats['ordens'],
            'rotulo' => __('OS'),
        ];
    }
@endphp

<div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 border-b border-zinc-100 px-4 py-2.5 text-xs text-zinc-500 sm:px-6 dark:border-white/5 dark:text-zinc-400">
    @foreach ($itens as $item)
        <span class="inline-flex items-center gap-2">
            <flux:icon :icon="$item['icone']" class="size-3.5 shrink-0 {{ $item['classe'] }}" />
            <span class="inline-flex items-baseline gap-1">
                <strong class="font-semibold text-zinc-900 dark:text-white">{{ $item['valor'] }}</strong>
                @if ($item['rotulo'])
                    <span class="whitespace-nowrap">{{ $item['rotulo'] }}</span>
                @endif
            </span>
        </span>
    @endforeach
</div>