<div class="flex h-full w-full flex-1 flex-col">
    @php
        $fmtBytes = fn (?int $bytes): string => $bytes === null ? '—' : ($bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', '.').' MB'
            : ($bytes >= 1024 ? number_format($bytes / 1024, 0, ',', '.').' KB' : $bytes.' B'));

        $fmtData = fn ($data): string => $data?->format('d/m/Y') ?? '—';
    @endphp

    {{-- Toolbar --}}
    <x-storage.toolbar
        :breadcrumbs="$this->breadcrumbs"
        :view="$view"
        :show-arvore="$showArvore"
        :estacao-id="$estacaoId"
    />

    {{-- Stats bar --}}
    <x-storage.stats :stats="$this->stats" :estacao-id="$estacaoId" />

    {{-- Body --}}
    <div class="flex min-h-0 flex-1">
        {{-- File tree sidebar --}}
        @if ($showArvore)
            <x-storage.tree
                :arvore="$this->arvore"
                :expandidos="$expandidos"
                :estacao-id="$estacaoId"
                :ordem-servico-id="$ordemServicoId"
                :radio-link-id="$radioLinkId"
            />
        @endif

        {{-- Content --}}
        <div class="flex-1 overflow-x-hidden p-4 sm:p-6">
            @if ($this->itens->isEmpty())
                <x-ui.empty
                    :title="$search !== '' ? __('Nenhum resultado encontrado') : __('Pasta vazia')"
                    :description="$search !== ''
                        ? __('Tente ajustar sua busca para encontrar o que procura.')
                        : ($estacaoId !== null
                            ? __('Envie arquivos para esta estação ou navegue pelas ordens de serviço.')
                            : __('As estações aparecem aqui como pastas. Navegue para acessar os arquivos.'))"
                >
                    @if ($search !== '')
                        <flux:button wire:click="$set('search', '')" variant="subtle" size="sm" icon="arrow-path">
                            {{ __('Limpar busca') }}
                        </flux:button>
                    @elseif ($estacaoId !== null)
                        <flux:button wire:click="abrirUpload" variant="primary" size="sm" icon="plus">
                            {{ __('Enviar arquivo') }}
                        </flux:button>
                    @endif
                </x-ui.empty>
            @elseif ($view === 'grade')
                {{-- Grid view --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach ($this->itens as $item)
                        <x-storage.grid-item :item="$item" :fmt-bytes="$fmtBytes" />
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
                            <x-storage.list-item :item="$item" :fmt-bytes="$fmtBytes" :fmt-data="$fmtData" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Upload modal --}}
    <x-storage.upload-modal
        :show-upload-modal="$showUploadModal"
        :ordem-servico-id="$ordemServicoId"
        :radio-link-id="$radioLinkId"
    />
</div>