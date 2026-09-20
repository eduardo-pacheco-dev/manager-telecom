@php
    $statusBadgeColors = [
        'Em implantação' => 'amber',
        'Ativo' => 'emerald',
        'Inativo' => 'gray',
        'Desativado' => 'red',
        'Cancelado' => 'red',
    ];
    $statusBadgeColor = $statusBadgeColors[$this->radioLink->status] ?? 'gray';

    $polarizacaoBadgeColors = [
        'Horizontal' => 'sky',
        'Vertical' => 'violet',
        'Dupla' => 'amber',
    ];
    $polarizacaoBadgeColor = $polarizacaoBadgeColors[$this->radioLink->polarizacao] ?? 'gray';

    $fmtNumber = fn ($value, int $decimals = 2): string => $value !== null
        ? number_format((float) $value, $decimals, ',', '.')
        : '—';
    $fmtDate = fn ($value): string => $value?->format('d/m/Y') ?? '—';

    $fmtBytes = function (?int $bytes): string {
        if ($bytes === null) {
            return '—';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.').' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return $bytes.' B';
    };

    $estacaoA = $this->radioLink->estacaoA;
    $estacaoB = $this->radioLink->estacaoB;

    $temCoordenadas = $estacaoA->latitude !== null && $estacaoA->longitude !== null
        && $estacaoB->latitude !== null && $estacaoB->longitude !== null;

    $navSections = [
        ['id' => 'estacoes', 'label' => __('Estações'), 'icon' => 'signal'],
        ['id' => 'mapa', 'label' => __('Mapa'), 'icon' => 'map'],
        ['id' => 'configuracao', 'label' => __('Configuração'), 'icon' => 'adjustments-horizontal'],
        ['id' => 'anexos', 'label' => __('Anexos'), 'icon' => 'paper-clip'],
        ['id' => 'comentarios', 'label' => __('Comentários'), 'icon' => 'chat-bubble-left-right'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6 scroll-smooth">
    {{-- Header --}}
    <div class="animate-fade-in-up flex flex-wrap items-center gap-3">
        <flux:button
            href="{{ route('radio-links.index') }}"
            wire:navigate
            icon="arrow-left"
            variant="ghost"
            size="sm"
            :aria-label="__('Voltar para radio links')"
        />

        <flux:breadcrumbs class="min-w-0 flex-1">
            <flux:breadcrumbs.item :href="route('radio-links.index')" wire:navigate>{{ __('Radio Links') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item class="truncate">{{ $this->radioLink->codigo }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <div class="flex shrink-0 items-center gap-2">
            <flux:button href="{{ route('radio-links.edit', $this->radioLink) }}" wire:navigate variant="primary" icon="pencil">
                {{ __('Editar') }}
            </flux:button>
            <flux:button
                wire:click="confirmDelete"
                variant="danger"
                icon="trash"
                :aria-label="__('Excluir')"
                :title="__('Excluir')"
            />
        </div>
    </div>

    {{-- Hero --}}
    <section class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-gradient-to-br from-white via-white to-sky-50/70 p-6 sm:p-8 dark:border-white/10 dark:from-white/[0.06] dark:via-white/[0.03] dark:to-sky-400/[0.05]" style="animation-delay: 40ms">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-sky-500/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-violet-500/10 blur-3xl dark:bg-violet-400/15"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="flex min-w-0 items-center gap-4">
                <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-violet-600 text-lg font-bold tracking-wide text-white shadow-lg shadow-sky-500/30 ring-4 ring-sky-500/10">
                    <flux:icon.radio class="size-7" />
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->radioLink->codigo }}</h2>
                        @if ($this->radioLink->status)
                            <flux:badge :color="$statusBadgeColor" rounded>{{ $this->radioLink->status }}</flux:badge>
                        @endif
                        @if ($this->radioLink->polarizacao)
                            <flux:badge :color="$polarizacaoBadgeColor" rounded>{{ $this->radioLink->polarizacao }}</flux:badge>
                        @endif
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <flux:icon.signal class="size-4 text-sky-500 dark:text-sky-400" />
                            {{ $estacaoA->site_id }}
                        </span>
                        <flux:icon.arrow-right class="size-4 text-zinc-300 dark:text-zinc-600" />
                        <span class="inline-flex items-center gap-1">
                            <flux:icon.signal class="size-4 text-violet-500 dark:text-violet-400" />
                            {{ $estacaoB->site_id }}
                        </span>
                        @if ($this->radioLink->nome)
                            <span class="text-zinc-300 dark:text-zinc-600">·</span>
                            <span>{{ $this->radioLink->nome }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 sm:grid-cols-4 lg:gap-x-10">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Frequência') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->radioLink->frequencia !== null ? $fmtNumber($this->radioLink->frequencia, 3).' GHz' : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Capacidade') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->radioLink->capacidade ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Distância') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $this->radioLink->distancia !== null ? $fmtNumber($this->radioLink->distancia).' km' : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Ativação') }}</p>
                    <p class="mt-1.5 text-sm font-semibold text-zinc-900 dark:text-white">{{ $fmtDate($this->radioLink->data_ativacao) }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick nav (scrollspy) --}}
    <nav
        x-data="{ active: '{{ $navSections[0]['id'] }}' }"
        @scroll.window.passive="
            const offset = 160;
            let current = '{{ $navSections[0]['id'] }}';
            document.querySelectorAll('[data-section]').forEach((el) => {
                if (el.getBoundingClientRect().top <= offset) current = el.id;
            });
            active = current;
        "
        class="animate-fade-in-up sticky top-4 z-20 flex gap-1.5 overflow-x-auto rounded-2xl border border-zinc-200 bg-white/90 p-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90"
        style="animation-delay: 80ms"
        aria-label="{{ __('Navegação rápida') }}"
    >
        @foreach ($navSections as $section)
            <a
                href="#{{ $section['id'] }}"
                @click="active = '{{ $section['id'] }}'"
                :class="active === '{{ $section['id'] }}'
                    ? 'bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-900'
                    : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white'"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
            >
                <flux:icon :icon="$section['icon']" variant="micro" class="size-4" />
                {{ $section['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Estações --}}
    <section id="estacoes" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                <flux:icon.signal class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Estações') }}</h3>
        </header>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ([
                ['label' => __('Estação A'), 'estacao' => $estacaoA, 'color' => 'sky'],
                ['label' => __('Estação B'), 'estacao' => $estacaoB, 'color' => 'violet'],
            ] as $item)
                @php
                    $est = $item['estacao'];
                    $color = $item['color'];
                    $colorText = $color === 'sky' ? 'text-sky-600 dark:text-sky-400' : 'text-violet-600 dark:text-violet-400';
                    $colorBg = $color === 'sky' ? 'bg-sky-500/10' : 'bg-violet-500/10';
                    $cidadeUf = trim(($est->municipio ?? '').($est->estado ? ' - '.$est->estado : '')) ?: '—';
                @endphp
                <div class="rounded-xl border border-zinc-200 p-4 transition-colors hover:bg-zinc-50/80 dark:border-white/10 dark:hover:bg-white/[0.02]">
                    <p class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        <span class="inline-flex size-4 items-center justify-center rounded {{ $colorBg }} {{ $colorText }}">{{ $loop->index === 0 ? 'A' : 'B' }}</span>
                        {{ $item['label'] }}
                    </p>
                    <a href="{{ route('estacoes.show', $est) }}" wire:navigate class="mt-3 block text-lg font-semibold {{ $colorText }} transition-colors hover:underline">
                        {{ $est->site_id }}
                    </a>
                    <dl class="mt-3 flex flex-col gap-1.5 text-sm">
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Município') }}</dt>
                            <dd class="min-w-0 truncate text-right font-medium text-zinc-700 dark:text-zinc-300">{{ $cidadeUf }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Tecnologia') }}</dt>
                            <dd class="min-w-0 truncate text-right font-medium text-zinc-700 dark:text-zinc-300">{{ $est->tecnologia ?? '—' }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-3">
                            <dt class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Status') }}</dt>
                            <dd class="min-w-0 truncate text-right font-medium text-zinc-700 dark:text-zinc-300">{{ $est->status ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Mapa --}}
    <section id="mapa" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 160ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                <flux:icon.map class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Mapa') }}</h3>
            @if ($temCoordenadas)
                <a
                    href="https://www.google.com/maps?saddr={{ $estacaoA->latitude }},{{ $estacaoA->longitude }}&daddr={{ $estacaoB->latitude }},{{ $estacaoB->longitude }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="ml-auto inline-flex items-center gap-1 text-xs font-medium text-sky-600 transition-colors hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300"
                >
                    <flux:icon.arrow-top-right-on-square class="size-3.5" />
                    {{ __('Abrir trajeto no mapa') }}
                </a>
            @endif
        </header>

        @if ($temCoordenadas)
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-white/10">
                <div
                    class="radio-link-map"
                    data-map
                    data-map-lat-a="{{ $estacaoA->latitude }}"
                    data-map-lng-a="{{ $estacaoA->longitude }}"
                    data-map-lat-b="{{ $estacaoB->latitude }}"
                    data-map-lng-b="{{ $estacaoB->longitude }}"
                    data-map-site-a="{{ $estacaoA->site_id }}"
                    data-map-site-b="{{ $estacaoB->site_id }}"
                ></div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="flex items-center gap-3 rounded-xl border border-zinc-100 bg-zinc-50/60 p-3.5 dark:border-white/5 dark:bg-white/[0.02]">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-xs font-bold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">A</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $estacaoA->site_id }}</p>
                        <p class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400">
                            {{ $fmtNumber($estacaoA->latitude, 6) }}, {{ $fmtNumber($estacaoA->longitude, 6) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-zinc-100 bg-zinc-50/60 p-3.5 dark:border-white/5 dark:bg-white/[0.02]">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-xs font-bold text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">B</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $estacaoB->site_id }}</p>
                        <p class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400">
                            {{ $fmtNumber($estacaoB->latitude, 6) }}, {{ $fmtNumber($estacaoB->longitude, 6) }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 p-10 text-center dark:border-white/10">
                <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                    <flux:icon.map class="size-6 text-zinc-400 dark:text-zinc-500" />
                </div>
                <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">{{ __('Mapa indisponível') }}</p>
                <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Adicione latitude e longitude às estações A e B para visualizar o trajeto do enlace.') }}
                </p>
            </div>
        @endif
    </section>

    {{-- Configuração --}}
    <section id="configuracao" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 200ms">
        <header class="mb-6 flex items-center gap-3">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                <flux:icon.adjustments-horizontal class="size-4.5" />
            </div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Configuração') }}</h3>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['label' => __('Frequência'), 'value' => $this->radioLink->frequencia !== null ? $fmtNumber($this->radioLink->frequencia, 3).' GHz' : null],
                ['label' => __('Capacidade'), 'value' => $this->radioLink->capacidade],
                ['label' => __('Canal'), 'value' => $this->radioLink->canal],
                ['label' => __('Polarização'), 'value' => $this->radioLink->polarizacao],
                ['label' => __('Fabricante'), 'value' => $this->radioLink->fabricante],
                ['label' => __('Modelo'), 'value' => $this->radioLink->modelo],
                ['label' => __('Distância'), 'value' => $this->radioLink->distancia !== null ? $fmtNumber($this->radioLink->distancia).' km' : null],
                ['label' => __('Data de ativação'), 'value' => $fmtDate($this->radioLink->data_ativacao)],
            ] as $item)
                <div class="rounded-xl border border-zinc-200 p-4 dark:border-white/10">
                    <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $item['label'] }}</p>
                    <p class="mt-1.5 text-sm font-medium text-zinc-900 dark:text-white">{{ $item['value'] ?? '—' }}</p>
                </div>
            @endforeach
        </div>

        @if ($this->radioLink->observacao)
            <div class="mt-4 rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
                <p class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    <flux:icon.chat-bubble-left-ellipsis variant="micro" class="size-3.5" />
                    {{ __('Observação') }}
                </p>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $this->radioLink->observacao }}</p>
            </div>
        @endif
    </section>

    {{-- Anexos --}}
    <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 240ms">
        <header class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.paper-clip class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Anexos') }}</h3>
            </div>
            @if ($anexos->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                    {{ $anexos->count() }} {{ __('arquivo(s)') }}
                </span>
            @endif
        </header>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <flux:input type="file" wire:model="anexo_arquivo" />
                <flux:error name="anexo_arquivo" />
            </div>
            <flux:button
                wire:click="saveAnexo"
                variant="primary"
                icon="arrow-up-tray"
                wire:loading.attr="disabled"
                wire:target="saveAnexo"
            >
                {{ __('Anexar arquivo') }}
            </flux:button>
        </div>

        <div class="mt-6">
            @forelse ($anexos as $anexo)
                <div wire:key="anexo-{{ $anexo->id }}" class="flex items-center gap-3 border-t border-zinc-100 py-3.5 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                    <x-ui.file-icon :mime="$anexo->mime" container="size-10" icon="size-5" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $anexo->nome }}</p>
                        <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $fmtBytes($anexo->tamanho) }} · {{ $anexo->created_at?->format('d/m/Y') }}
                        </p>
                    </div>
                    <a
                        href="{{ route('radio-links.anexos.download', $anexo) }}"
                        class="inline-flex items-center rounded-lg px-2 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        aria-label="{{ __('Baixar') }}"
                    >
                        <flux:icon.arrow-down-tray class="size-4" />
                    </a>
                    <flux:button
                        wire:click="removerAnexo({{ $anexo->id }})"
                        wire:confirm="{{ __('Remover este anexo?') }}"
                        variant="ghost"
                        icon="trash"
                        size="sm"
                        :aria-label="__('Remover anexo')"
                    />
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                    {{ __('Nenhum anexo registrado. Adicione laudos, projetos ou documentos do enlace.') }}
                </p>
            @endforelse
        </div>
    </section>

    {{-- Comentários --}}
    <section id="comentarios" data-section class="animate-fade-in-up scroll-mt-24 rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6 dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 280ms">
        <header class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.chat-bubble-left-right class="size-4.5" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ __('Comentários') }}</h3>
            </div>
            @if ($comentarios->isNotEmpty())
                <span class="inline-flex items-center rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                    {{ $comentarios->count() }} {{ __('comentário(s)') }}
                </span>
            @endif
        </header>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <flux:textarea wire:model="comentario" rows="3" placeholder="{{ __('Escreva um comentário sobre o enlace...') }}" />
                <flux:error name="comentario" />
            </div>
            <flux:button
                wire:click="addComentario"
                variant="primary"
                icon="paper-airplane"
                wire:loading.attr="disabled"
                wire:target="addComentario"
            >
                {{ __('Comentar') }}
            </flux:button>
        </div>

        <div class="mt-6">
            @forelse ($comentarios as $comentario)
                <div wire:key="comentario-{{ $comentario->id }}" class="flex gap-3 border-t border-zinc-100 py-4 first:border-t-0 first:pt-0 last:pb-0 dark:border-white/5">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-500/10 text-xs font-bold text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                        {{ \Illuminate\Support\Str::initials($comentario->user->name, true) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-2">
                                <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $comentario->user->name }}</p>
                                <span class="shrink-0 text-xs text-zinc-400 dark:text-zinc-500">{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if ($comentario->user_id === auth()->id())
                                <flux:button
                                    wire:click="removerComentario({{ $comentario->id }})"
                                    wire:confirm="{{ __('Remover este comentário?') }}"
                                    variant="ghost"
                                    icon="trash"
                                    size="sm"
                                    :aria-label="__('Remover comentário')"
                                />
                            @endif
                        </div>
                        <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $comentario->conteudo }}</p>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-200 p-6 text-center text-sm text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                    {{ __('Nenhum comentário ainda. Seja a primeira pessoa a comentar.') }}
                </p>
            @endforelse
        </div>
    </section>
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir radio link') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->radioLink->codigo }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif