<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    @php
        $fmtBytes = fn (?int $bytes): string => $bytes === null ? '0 B' : ($bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));
    @endphp

    {{-- Hero / Page header --}}
    <div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-emerald-500/5 dark:from-sky-400/10 dark:via-transparent dark:to-emerald-400/10"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 size-56 rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-400/15"></div>

        <div class="relative flex flex-col gap-5 p-5 sm:p-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item>{{ __('Gestão') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Storage') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <flux:heading size="xl" level="1" class="flex items-center gap-3">
                        {{ __('Storage') }}
                        <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-sky-500/10 px-2.5 py-0.5 text-sm font-semibold text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                            {{ $this->stats['arquivos'] }}
                        </span>
                    </flux:heading>
                    <flux:subheading size="lg" class="mt-1">{{ __('Gerencie os arquivos organizados por estação e ordem de serviço') }}</flux:subheading>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Resumo') }}">
        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-zinc-200/40 blur-2xl dark:bg-white/5"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Arquivos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->stats['arquivos'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.archive-box class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-zinc-400 to-zinc-600 dark:from-zinc-500 dark:to-zinc-300" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Inventário completo') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-sky-200/40 blur-2xl dark:bg-sky-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Espaço usado') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-sky-600 dark:text-sky-400">{{ $fmtBytes($this->stats['tamanho']) }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 transition-transform group-hover:scale-105 dark:bg-sky-400/10 dark:text-sky-400">
                    <flux:icon.server class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-sky-500/10 dark:bg-sky-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-sky-400" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Total em anexos') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Estações') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->stats['estacoes'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.signal class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-emerald-500/10 dark:bg-emerald-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Organização do storage') }}</p>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 190ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-violet-200/40 blur-2xl dark:bg-violet-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Ordens de serviço') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-violet-600 dark:text-violet-400">{{ $this->stats['ordens'] }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 transition-transform group-hover:scale-105 dark:bg-violet-400/10 dark:text-violet-400">
                    <flux:icon.clipboard-document-list class="size-5" />
                </div>
            </div>
            <div class="relative mt-4">
                <div class="h-1.5 overflow-hidden rounded-full bg-violet-500/10 dark:bg-violet-400/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-violet-400" style="width: 100%"></div>
                </div>
                <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Anexos por OS') }}</p>
            </div>
        </div>
    </section>

    {{-- Toolbar --}}
    <div class="animate-fade-in-up sticky top-4 z-20 rounded-2xl border border-zinc-200 bg-white/90 p-3 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 230ms">
        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
            <flux:input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('Buscar por estação, município, arquivo ou OS...')"
                icon="magnifying-glass"
            />

            <flux:select wire:model.live="perPage" class="w-full sm:w-32" :label="__('Estações por página')">
                <flux:select.option value="10">10</flux:select.option>
                <flux:select.option value="25">25</flux:select.option>
                <flux:select.option value="50">50</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Stations list --}}
    <div class="flex flex-col gap-5">
        @forelse ($estacoes as $estacao)
            @php
                $radioLinks = $estacao->radioLinksRelacionados();
                $ordens = $estacao->ordensServicoRelacionadas();
                $totalArquivos = $estacao->anexos->count()
                    + $radioLinks->sum(fn ($link) => $link->anexos->count())
                    + $ordens->sum(fn ($os) => $os->anexos->count());

                $statusBadgeColors = [
                    'CANDIDATO A' => 'amber',
                    'Aquisitado' => 'sky',
                    'Adquirido' => 'sky',
                    'Em construção' => 'amber',
                    'Ativo' => 'emerald',
                    'Inativo' => 'gray',
                    'Desativado' => 'red',
                    'Cancelado' => 'red',
                ];
                $statusBadgeColor = $statusBadgeColors[$estacao->status] ?? 'gray';
            @endphp

            <section wire:key="estacao-{{ $estacao->id }}" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 260ms">
                {{-- Station header --}}
                <div class="flex flex-col gap-3 border-b border-zinc-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 text-sky-700 shadow-sm ring-1 ring-black/5 dark:bg-sky-400/10 dark:text-sky-300">
                            <flux:icon.signal class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('estacoes.show', $estacao) }}" wire:navigate class="truncate text-base font-semibold text-zinc-900 transition-colors hover:text-sky-600 dark:text-white dark:hover:text-sky-400">
                                    {{ $estacao->site_id }}
                                </a>
                                @if ($estacao->status)
                                    <flux:badge :color="$statusBadgeColor" rounded>{{ $estacao->status }}</flux:badge>
                                @endif
                            </div>
                            <p class="mt-0.5 truncate text-xs text-zinc-500 dark:text-zinc-400">
                                @if ($estacao->municipio)
                                    {{ $estacao->municipio }}@if ($estacao->estado) · {{ $estacao->estado }}@endif
                                @else
                                    {{ __('Sem município') }}
                                @endif
                                · {{ $totalArquivos }} {{ __('arquivo(s)') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <flux:button
                            href="{{ route('estacoes.show', $estacao) }}"
                            wire:navigate
                            size="sm"
                            variant="ghost"
                            icon="eye"
                        >
                            {{ __('Estação') }}
                        </flux:button>
                        <flux:button
                            wire:click="abrirUploadEstacao({{ $estacao->id }})"
                            size="sm"
                            variant="filled"
                            icon="arrow-up-tray"
                        >
                            {{ __('Enviar arquivo') }}
                        </flux:button>
                    </div>
                </div>

                <div class="grid gap-6 p-5 lg:grid-cols-3">
                    {{-- Station files --}}
                    <div class="min-w-0">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <flux:icon.folder class="size-3.5 text-sky-500 dark:text-sky-400" />
                            {{ __('Arquivos da estação') }}
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">{{ $estacao->anexos->count() }}</span>
                        </p>

                        @forelse ($estacao->anexos as $anexo)
                            @include('livewire.storage.partials.anexo', [
                                'anexo' => $anexo,
                                'downloadRoute' => route('estacoes.anexos.download', $anexo),
                                'deleteMethod' => 'removerAnexoEstacao('.$anexo->id.')',
                            ])
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-4 text-center text-xs text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                {{ __('Nenhum arquivo') }}
                            </p>
                        @endforelse
                    </div>

                    {{-- Radio links files --}}
                    <div class="min-w-0">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <flux:icon.radio class="size-3.5 text-violet-500 dark:text-violet-400" />
                            {{ __('Radio Links') }}
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">{{ $radioLinks->count() }}</span>
                        </p>

                        @forelse ($radioLinks as $radioLink)
                            <div class="mb-4 last:mb-0">
                                <div class="mb-1.5 flex items-center gap-1.5">
                                    <a href="{{ route('radio-links.show', $radioLink) }}" wire:navigate class="text-sm font-medium text-violet-600 transition-colors hover:underline dark:text-violet-400">
                                        {{ $radioLink->codigo }}
                                    </a>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $radioLink->anexos->count() }}</span>
                                </div>
                                @forelse ($radioLink->anexos as $anexo)
                                    @include('livewire.storage.partials.anexo', [
                                        'anexo' => $anexo,
                                        'downloadRoute' => route('radio-links.anexos.download', $anexo),
                                        'deleteMethod' => 'removerAnexoRadioLink('.$anexo->id.')',
                                    ])
                                @empty
                                    <p class="rounded-lg border border-dashed border-zinc-200 p-3 text-center text-xs text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                        {{ __('Sem anexos') }}
                                    </p>
                                @endforelse
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-4 text-center text-xs text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                {{ __('Nenhum radio link') }}
                            </p>
                        @endforelse
                    </div>

                    {{-- OS files --}}
                    <div class="min-w-0">
                        <p class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <flux:icon.clipboard-document-list class="size-3.5 text-emerald-500 dark:text-emerald-400" />
                            {{ __('Ordens de Serviço') }}
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">{{ $ordens->count() }}</span>
                        </p>

                        @forelse ($ordens as $os)
                            <div class="mb-4 last:mb-0">
                                <div class="mb-1.5 flex items-center gap-1.5">
                                    <a href="{{ route('ordens-servico.show', $os) }}" wire:navigate class="text-sm font-medium text-emerald-600 transition-colors hover:underline dark:text-emerald-400">
                                        {{ $os->codigo }}
                                    </a>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $os->anexos->count() }}</span>
                                </div>
                                @forelse ($os->anexos as $anexo)
                                    @include('livewire.storage.partials.anexo', [
                                        'anexo' => $anexo,
                                        'downloadRoute' => route('ordens-servico.anexos.download', $anexo),
                                        'deleteMethod' => 'removerAnexoOrdem('.$anexo->id.')',
                                    ])
                                @empty
                                    <p class="rounded-lg border border-dashed border-zinc-200 p-3 text-center text-xs text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                        {{ __('Sem anexos') }}
                                    </p>
                                @endforelse
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-zinc-200 p-4 text-center text-xs text-zinc-400 dark:border-white/10 dark:text-zinc-500">
                                {{ __('Nenhuma ordem de serviço') }}
                            </p>
                        @endforelse
                    </div>
                </div>
            </section>
        @empty
            <div class="flex flex-col items-center justify-center rounded-2xl border border-zinc-200 bg-white px-6 py-16 text-center dark:border-white/10 dark:bg-white/[0.03]">
                <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                    <flux:icon.archive-box class="size-7 text-zinc-400 dark:text-zinc-500" />
                </div>
                <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                    {{ __('Nenhuma estação encontrada') }}
                </p>
                <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Tente ajustar sua busca para encontrar o que procura.') }}
                </p>
                @if ($search !== '')
                    <flux:button wire:click="$set('search', '')" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                        {{ __('Limpar busca') }}
                    </flux:button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($estacoes->hasPages())
        <div class="flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white px-5 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-white/[0.03]">
            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Mostrando') }} {{ $estacoes->firstItem() }} {{ __('a') }} {{ $estacoes->lastItem() }} {{ __('de') }} {{ $estacoes->total() }} {{ __('estações') }}
            </div>
            <div class="flex items-center gap-1">
                @if ($estacoes->onFirstPage())
                    <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-left variant="micro" />
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage"
                        :aria-label="__('Página anterior')"
                        class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <flux:icon.chevron-left variant="micro" />
                    </button>
                @endif

                @foreach ($estacoes->getUrlRange(max(1, $estacoes->currentPage() - 1), min($estacoes->lastPage(), $estacoes->currentPage() + 1)) as $page => $url)
                    @if ($page == $estacoes->currentPage())
                        <span class="flex size-8 items-center justify-center rounded-lg bg-zinc-900 text-xs font-medium text-white dark:bg-white dark:text-zinc-900">
                            {{ $page }}
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="gotoPage({{ $page }})"
                            class="flex size-8 items-center justify-center rounded-lg text-xs font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            {{ $page }}
                        </button>
                    @endif
                @endforeach

                @if ($estacoes->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage"
                        :aria-label="__('Próxima página')"
                        class="flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <flux:icon.chevron-right variant="micro" />
                    </button>
                @else
                    <span class="flex size-8 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-right variant="micro" />
                    </span>
                @endif
            </div>
        </div>
    @endif

    {{-- Upload modal --}}
    <flux:modal wire:model="showUploadModal" class="max-w-lg">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">{{ __('Enviar arquivo') }}</flux:heading>
                <flux:text class="mt-2">
                    @if ($ordemServicoId !== null)
                        {{ __('O arquivo será vinculado à ordem de serviço selecionada.') }}
                    @else
                        {{ __('O arquivo será vinculado à estação selecionada.') }}
                    @endif
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Arquivo') }} <span class="text-rose-500">*</span></flux:label>
                    <flux:input type="file" wire:model="arquivo" />
                    <flux:error name="arquivo" />
                </flux:field>

                <div class="rounded-xl border border-zinc-100 bg-zinc-50/60 p-3 text-xs text-zinc-500 dark:border-white/5 dark:bg-white/[0.02] dark:text-zinc-400">
                    {{ __('Formatos aceitos: qualquer tipo de arquivo até 20 MB.') }}
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <flux:modal.close>
                    <flux:button variant="filled" wire:click="fecharUpload">{{ __('Cancelar') }}</flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    icon="arrow-up-tray"
                    wire:click="salvarArquivo"
                    wire:loading.attr="disabled"
                    wire:target="salvarArquivo"
                >
                    {{ __('Enviar') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>