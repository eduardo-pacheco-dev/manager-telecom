<div class="flex h-full w-full flex-1 flex-col">
    @php
        $fmtBytes = fn (?int $bytes): string => $bytes === null ? '—' : ($bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));

        $fmtData = fn ($data): string => $data?->format('d/m/Y') ?? '—';

        $iconeItem = function (array $item): string {
            if ($item['tipo'] !== 'arquivo_estacao' && $item['tipo'] !== 'arquivo_ordem' && $item['tipo'] !== 'arquivo_radio') {
                return 'folder';
            }
            $mime = $item['mime'] ?? null;
            if ($mime !== null && str_starts_with($mime, 'image/')) {
                return 'photo';
            }
            if ($mime === 'application/pdf') {
                return 'document-text';
            }

            return 'paper-clip';
        };

        $classeItem = function (array $item): string {
            if ($item['tipo'] !== 'arquivo_estacao' && $item['tipo'] !== 'arquivo_ordem' && $item['tipo'] !== 'arquivo_radio') {
                return 'bg-amber-400/15 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400';
            }
            $mime = $item['mime'] ?? null;
            if ($mime !== null && str_starts_with($mime, 'image/')) {
                return 'bg-violet-500/15 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400';
            }
            if ($mime === 'application/pdf') {
                return 'bg-rose-500/15 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400';
            }

            return 'bg-zinc-700/15 text-zinc-600 dark:bg-white/10 dark:text-zinc-300';
        };

        $ePasta = fn (array $item): bool => ! in_array($item['tipo'], ['arquivo_estacao', 'arquivo_ordem', 'arquivo_radio'], true);
    @endphp

    {{-- Toolbar --}}
    <div class="sticky top-0 z-20 border-b border-zinc-200 bg-white/95 backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/95">
        <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:gap-3 sm:px-6">
            {{-- Breadcrumb --}}
            <nav class="flex min-w-0 items-center gap-1 overflow-x-auto" aria-label="{{ __('Navegação') }}">
                <flux:button
                    wire:click="voltarRaiz"
                    variant="ghost"
                    size="sm"
                    icon="arrow-left"
                    :aria-label="__('Voltar')"
                    class="shrink-0"
                />
                @foreach ($this->breadcrumbs as $index => $crumb)
                    @if ($index > 0)
                        <flux:icon.chevron-right class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
                    @endif
                    @if ($crumb['acao'])
                        <button
                            type="button"
                            wire:click="{{ $crumb['acao'] }}"
                            class="shrink-0 cursor-pointer rounded-lg px-2 py-1 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                        >
                            {{ $crumb['label'] }}
                        </button>
                    @else
                        <span class="shrink-0 px-2 py-1 text-sm font-semibold text-zinc-900 dark:text-white">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <div class="relative flex-1 sm:flex-none">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        :placeholder="__('Buscar...')"
                        icon="magnifying-glass"
                        class="w-full sm:w-56"
                    />
                </div>

                <flux:select wire:model.live="sortField" class="hidden w-36 md:block">
                    <flux:select.option value="nome">{{ __('Nome') }}</flux:select.option>
                    <flux:select.option value="data">{{ __('Modificado') }}</flux:select.option>
                    <flux:select.option value="tamanho">{{ __('Tamanho') }}</flux:select.option>
                </flux:select>

                <flux:button
                    wire:click="alternarView"
                    variant="ghost"
                    size="sm"
                    :icon="$view === 'lista' ? 'squares-2x2' : 'list-bullet'"
                    :title="$view === 'lista' ? __('Exibir em grade') : __('Exibir em lista')"
                    :aria-label="$view === 'lista' ? __('Exibir em grade') : __('Exibir em lista')"
                />

                @if ($estacaoId !== null)
                    <flux:button wire:click="abrirUpload" variant="primary" icon="plus">
                        {{ __('Novo') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats bar --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1 border-b border-zinc-100 px-4 py-2 text-xs text-zinc-500 sm:px-6 dark:border-white/5 dark:text-zinc-400">
        <span class="inline-flex items-center gap-1.5">
            <flux:icon.archive-box class="size-3.5" />
            {{ $this->stats['arquivos'] }} {{ __('arquivo(s)') }}
        </span>
        <span class="inline-flex items-center gap-1.5">
            <flux:icon.server class="size-3.5" />
            {{ $fmtBytes($this->stats['tamanho']) }}
        </span>
        @if ($estacaoId === null)
            <span class="inline-flex items-center gap-1.5">
                <flux:icon.signal class="size-3.5" />
                {{ $this->stats['estacoes'] }} {{ __('estação(ões)') }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <flux:icon.clipboard-document-list class="size-3.5" />
                {{ $this->stats['ordens'] }} {{ __('OS') }}
            </span>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-x-hidden p-4 sm:p-6">
        @if ($this->itens->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-20 text-center">
                <div class="flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                    <flux:icon.folder class="size-8 text-zinc-400 dark:text-zinc-500" />
                </div>
                <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                    {{ $search !== '' ? __('Nenhum resultado encontrado') : __('Pasta vazia') }}
                </p>
                <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                    @if ($search !== '')
                        {{ __('Tente ajustar sua busca para encontrar o que procura.') }}
                    @elseif ($estacaoId !== null)
                        {{ __('Envie arquivos para esta estação ou navegue pelas ordens de serviço.') }}
                    @else
                        {{ __('As estações aparecem aqui como pastas. Navegue para acessar os arquivos.') }}
                    @endif
                </p>
                @if ($search !== '')
                    <flux:button wire:click="$set('search', '')" variant="subtle" size="sm" icon="arrow-path" class="mt-5">
                        {{ __('Limpar busca') }}
                    </flux:button>
                @elseif ($estacaoId !== null)
                    <flux:button wire:click="abrirUpload" variant="primary" size="sm" icon="plus" class="mt-5">
                        {{ __('Enviar arquivo') }}
                    </flux:button>
                @endif
            </div>
        @elseif ($view === 'grade')
            {{-- Grid view --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($this->itens as $item)
                    <div wire:key="item-{{ $item['tipo'] }}-{{ $item['id'] }}" class="group relative">
                        @if ($ePasta($item))
                            <button
                                type="button"
                                wire:click="{{ $item['abrir'] }}"
                                class="flex w-full cursor-pointer flex-col items-center gap-2 rounded-2xl border border-zinc-200 bg-white p-4 text-center transition-all duration-150 hover:border-sky-300 hover:bg-sky-50/50 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-sky-500/50 dark:hover:bg-sky-400/5"
                            >
                                <div class="flex size-14 items-center justify-center rounded-2xl {{ $classeItem($item) }}">
                                    <flux:icon.folder class="size-7" />
                                </div>
                                <span class="line-clamp-2 text-xs font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</span>
                                <span class="truncate text-[11px] text-zinc-400 dark:text-zinc-500">{{ $item['subtitulo'] }}</span>
                            </button>
                        @else
                            <div class="flex w-full flex-col items-center gap-2 rounded-2xl border border-zinc-200 bg-white p-4 text-center transition-all duration-150 hover:border-zinc-300 dark:border-white/10 dark:bg-white/[0.03] dark:hover:border-white/20">
                                <div class="flex size-14 items-center justify-center rounded-2xl {{ $classeItem($item) }}">
                                    <flux:icon :icon="$iconeItem($item)" class="size-7" />
                                </div>
                                <span class="line-clamp-2 text-xs font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</span>
                                <span class="truncate text-[11px] text-zinc-400 dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</span>

                                <div class="absolute inset-0 flex items-center justify-center gap-2 rounded-2xl bg-zinc-900/60 opacity-0 backdrop-blur-[2px] transition-opacity duration-150 group-hover:opacity-100 dark:bg-zinc-950/70">
                                    <a
                                        href="{{ $item['download'] }}"
                                        class="flex size-9 items-center justify-center rounded-full bg-white/90 text-zinc-800 transition-transform hover:scale-110"
                                        :aria-label="__('Baixar')"
                                    >
                                        <flux:icon.arrow-down-tray class="size-4.5" />
                                    </a>
                                    <flux:button
                                        wire:click="{{ $item['remover'] }}"
                                        wire:confirm="{{ __('Remover este anexo?') }}"
                                        variant="danger"
                                        icon="trash"
                                        size="sm"
                                        :aria-label="__('Remover')"
                                        class="size-9 rounded-full"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {{-- List view --}}
            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-none dark:border-white/10 dark:bg-white/[0.03]">
                {{-- Header --}}
                <div class="hidden items-center gap-4 border-b border-zinc-100 bg-zinc-50/60 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:border-white/5 dark:bg-white/[0.02] dark:text-zinc-500 sm:flex">
                    <button type="button" wire:click="ordenar('nome')" class="flex min-w-0 flex-1 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                        {{ __('Nome') }}
                        @if ($sortField === 'nome')
                            <flux:icon :icon="$sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'" class="size-3" />
                        @endif
                    </button>
                    <button type="button" wire:click="ordenar('data')" class="hidden w-32 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Modificado') }}
                        @if ($sortField === 'data')
                            <flux:icon :icon="$sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'" class="size-3" />
                        @endif
                    </button>
                    <button type="button" wire:click="ordenar('tamanho')" class="hidden w-24 shrink-0 cursor-pointer items-center gap-1 text-left transition-colors hover:text-zinc-700 md:flex dark:hover:text-zinc-200">
                        {{ __('Tamanho') }}
                        @if ($sortField === 'tamanho')
                            <flux:icon :icon="$sortDirection === 'asc' ? 'arrow-up' : 'arrow-down'" class="size-3" />
                        @endif
                    </button>
                    <div class="w-16 shrink-0 text-right">{{ __('Ações') }}</div>
                </div>

                <div class="flex flex-col">
                    @foreach ($this->itens as $item)
                        @if ($ePasta($item))
                            <div wire:key="item-{{ $item['tipo'] }}-{{ $item['id'] }}" class="group flex items-center gap-3 border-b border-zinc-100 px-5 py-3 transition-colors last:border-b-0 hover:bg-sky-50/60 dark:border-white/5 dark:hover:bg-sky-400/5">
                                <button
                                    type="button"
                                    wire:click="{{ $item['abrir'] }}"
                                    class="flex min-w-0 flex-1 cursor-pointer items-center gap-3 text-left"
                                >
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $classeItem($item) }}">
                                        <flux:icon.folder class="size-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-zinc-900 group-hover:text-sky-700 dark:text-white dark:group-hover:text-sky-300">{{ $item['nome'] }}</p>
                                        <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ $item['subtitulo'] }}</p>
                                    </div>
                                </button>
                                <span class="hidden w-32 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtData($item['data']) }}</span>
                                <span class="hidden w-24 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">
                                    {{ (int) $item['tamanho'] }} {{ __('item(ns)') }}
                                </span>
                                <div class="flex w-16 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <flux:button
                                        wire:click="{{ $item['abrir'] }}"
                                        size="sm"
                                        variant="ghost"
                                        icon="eye"
                                        :title="__('Abrir')"
                                        :aria-label="__('Abrir') . ' ' . $item['nome']"
                                    />
                                </div>
                            </div>
                        @else
                            <div wire:key="item-{{ $item['tipo'] }}-{{ $item['id'] }}" class="group flex items-center gap-3 border-b border-zinc-100 px-5 py-3 transition-colors last:border-b-0 hover:bg-zinc-50/80 dark:border-white/5 dark:hover:bg-white/[0.02]">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $classeItem($item) }}">
                                        <flux:icon :icon="$iconeItem($item)" class="size-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $item['nome'] }}</p>
                                        <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</p>
                                    </div>
                                </div>
                                <span class="hidden w-32 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtData($item['data']) }}</span>
                                <span class="hidden w-24 shrink-0 text-xs text-zinc-400 md:block dark:text-zinc-500">{{ $fmtBytes($item['tamanho']) }}</span>
                                <div class="flex w-16 shrink-0 items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                    <a
                                        href="{{ $item['download'] }}"
                                        class="inline-flex size-8 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                                        :aria-label="__('Baixar') . ' ' . $item['nome']"
                                    >
                                        <flux:icon.arrow-down-tray class="size-4.5" />
                                    </a>
                                    <flux:button
                                        wire:click="{{ $item['remover'] }}"
                                        wire:confirm="{{ __('Remover este anexo?') }}"
                                        variant="ghost"
                                        icon="trash"
                                        size="sm"
                                        :aria-label="__('Remover') . ' ' . $item['nome']"
                                    />
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Upload modal --}}
    <flux:modal wire:model="showUploadModal" class="max-w-lg">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">{{ __('Enviar arquivo') }}</flux:heading>
                <flux:text class="mt-2">
                    @if ($ordemServicoId !== null)
                        {{ __('O arquivo será vinculado à ordem de serviço selecionada.') }}
                    @elseif ($radioLinkId !== null)
                        {{ __('O arquivo será vinculado ao radio link selecionado.') }}
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