@props(['arvore', 'expandidos', 'estacaoId', 'ordemServicoId', 'radioLinkId'])

<aside class="flex w-72 shrink-0 flex-col border-e border-zinc-200 p-3 dark:border-white/10">
    <p class="mb-2 flex items-center gap-2 px-2 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
        <flux:icon.folder class="size-3.5" />
        {{ __('Árvore de arquivos') }}
    </p>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="flex flex-col gap-0.5">
            {{-- Root --}}
            <button
                type="button"
                wire:click="voltarRaiz"
                class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium transition-colors {{ $estacaoId === null ? 'bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300' : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10' }}"
            >
                <flux:icon.server class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" />
                <span class="truncate">{{ __('Armazenamento') }}</span>
            </button>

            {{-- Stations --}}
            @foreach ($arvore as $estacao)
                @php
                    $estacaoChave = 'estacao-'.$estacao['id'];
                    $estacaoAberta = in_array($estacaoChave, $expandidos, true) || $estacaoId === $estacao['id'];
                    $estacaoAtiva = $estacaoId === $estacao['id'] && $ordemServicoId === null && $radioLinkId === null;
                @endphp
                <div wire:key="tree-{{ $estacaoChave }}">
                    <div class="flex items-center gap-1 rounded-lg pr-1 transition-colors {{ $estacaoAtiva ? 'bg-sky-50 dark:bg-sky-400/10' : 'hover:bg-zinc-100 dark:hover:bg-white/10' }}">
                        <button
                            type="button"
                            wire:click="alternarExpandido('{{ $estacaoChave }}')"
                            class="flex size-6 shrink-0 cursor-pointer items-center justify-center rounded text-zinc-400 hover:text-zinc-700 dark:text-zinc-500 dark:hover:text-zinc-200"
                            :aria-label="__('Expandir')"
                        >
                            <flux:icon :icon="$estacaoAberta ? 'chevron-down' : 'chevron-right'" class="size-3.5" />
                        </button>
                        <button
                            type="button"
                            wire:click="abrirEstacao({{ $estacao['id'] }})"
                            class="flex min-w-0 flex-1 cursor-pointer items-center gap-2 py-1.5 text-sm font-medium transition-colors {{ $estacaoAtiva ? 'text-sky-700 dark:text-sky-300' : 'text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white' }}"
                        >
                            <flux:icon.folder class="size-4 shrink-0 text-amber-500 dark:text-amber-400" />
                            <span class="truncate">{{ $estacao['nome'] }}</span>
                        </button>
                        <span class="shrink-0 text-[11px] tabular-nums text-zinc-400 dark:text-zinc-500">{{ $estacao['contagem'] }}</span>
                    </div>

                    @if ($estacaoAberta)
                        <div class="ml-4 border-s border-zinc-200 ps-2 dark:border-white/10">
                            @foreach ($estacao['filhos'] as $filho)
                                @php
                                    $filhoChave = $filho['tipo'].'-'.$filho['id'];
                                    $filhoAtivo = ($filho['tipo'] === 'radio' && $radioLinkId === $filho['id'])
                                        || ($filho['tipo'] === 'ordem' && $ordemServicoId === $filho['id']);
                                @endphp
                                <button
                                    type="button"
                                    wire:click="{{ $filho['tipo'] === 'radio' ? 'abrirRadioLink' : 'abrirOrdem' }}({{ $filho['id'] }})"
                                    class="flex w-full cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition-colors {{ $filhoAtivo ? 'bg-sky-50 font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white' }}"
                                >
                                    <flux:icon :icon="$filho['tipo'] === 'radio' ? 'radio' : 'clipboard-document-list'" class="size-3.5 shrink-0 {{ $filho['tipo'] === 'radio' ? 'text-violet-500 dark:text-violet-400' : 'text-emerald-500 dark:text-emerald-400' }}" />
                                    <span class="truncate">{{ $filho['nome'] }}</span>
                                    <span class="ms-auto shrink-0 text-[11px] tabular-nums text-zinc-400 dark:text-zinc-500">{{ $filho['contagem'] }}</span>
                                </button>
                            @endforeach

                            @if ($estacao['filhos']->isEmpty())
                                <p class="px-2 py-1.5 text-xs text-zinc-400 dark:text-zinc-500">{{ __('Sem subpastas') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Paginação da árvore --}}
    @if ($arvore->hasPages())
        <div class="mt-2 border-t border-zinc-200 pt-2 dark:border-white/10">
            <div class="flex items-center gap-1">
                @if ($arvore->onFirstPage())
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-left variant="micro" />
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage"
                        aria-label="{{ __('Página anterior') }}"
                        class="flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <flux:icon.chevron-left variant="micro" />
                    </button>
                @endif

                <span class="min-w-0 flex-1 truncate text-center text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $arvore->currentPage() }} / {{ $arvore->lastPage() }}
                </span>

                @if ($arvore->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage"
                        aria-label="{{ __('Próxima página') }}"
                        class="flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <flux:icon.chevron-right variant="micro" />
                    </button>
                @else
                    <span class="flex size-7 shrink-0 items-center justify-center rounded-lg text-zinc-300 dark:text-zinc-600">
                        <flux:icon.chevron-right variant="micro" />
                    </span>
                @endif
            </div>
        </div>
    @endif
</aside>