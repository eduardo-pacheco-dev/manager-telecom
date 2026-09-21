<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Configurações de Estações')"
        :subtitle="__('Gerencie os valores disponíveis nos formulários de estações')"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Estações'), 'href' => route('estacoes.index')],
            ['label' => __('Configurações'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('estacoes.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
        <flux:button wire:click="abrirNovo" variant="primary" icon="plus">
            {{ __('Novo registro') }}
        </flux:button>
    </x-ui.page-header>

    {{-- Tabs --}}
    <div class="animate-fade-in-up flex gap-1.5 overflow-x-auto rounded-2xl border border-zinc-200 bg-white/90 p-1.5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90">
        @php
            $abas = [
                'tecnologias' => ['cpu-chip', __('Tecnologias')],
                'tipos-conexao' => ['arrows-right-left', __('Tipos de conexão')],
                'enderecos' => ['map-pin', __('Endereços')],
                'stations' => ['identification', __('Station IDs')],
                'status' => ['signal', __('Status')],
                'detentores' => ['building-office', __('Detentores')],
                'tipos-infra' => ['building-library', __('Tipos de infra')],
                'tipos-ev' => ['bolt', __('Tipos de EV')],
            ];
        @endphp

        @foreach ($abas as $abaId => [$abaIcon, $abaLabel])
            <button
                type="button"
                wire:click="mudarAba('{{ $abaId }}')"
                :aria-label="$abaLabel"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-medium transition-all duration-200"
                @class([
                    'bg-zinc-900 text-white shadow-sm dark:bg-white dark:text-zinc-900' => $aba === $abaId,
                    'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-white' => $aba !== $abaId,
                ])
            >
                <flux:icon :icon="$abaIcon" variant="micro" class="size-4" />
                {{ $abaLabel }}
            </button>
        @endforeach
    </div>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-3" aria-label="{{ __('Resumo') }}">
        <x-ui.stat-card
            :label="__('Total')"
            :value="$this->itens->count()"
            icon="tag"
            color="zinc"
        />

        <x-ui.stat-card
            :label="__('Ativos')"
            :value="$this->itens->where('ativo', true)->count()"
            icon="check-circle"
            color="emerald"
            delay="90ms"
        />

        <x-ui.stat-card
            :label="__('Inativos')"
            :value="$this->itens->where('ativo', false)->count()"
            icon="x-circle"
            color="rose"
            delay="140ms"
        />
    </section>

    {{-- List --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 180ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Registros de') }} {{ $abas[$aba][1] }} {{ __('cadastrados') }}
            </p>
        </div>

        <div class="flex flex-col">
            @forelse ($this->itens as $item)
                <div wire:key="{{ $aba }}-{{ $item->id }}" class="group flex items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl shadow-sm ring-1 ring-black/5 {{ $item->ativo ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300' : 'bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500' }}">
                            <flux:icon :icon="$abas[$aba][0]" class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium {{ $item->ativo ? 'text-zinc-900 dark:text-white' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $item->nome }}</p>
                            @if ($item->descricao)
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $item->descricao }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($item->ativo)
                        <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">{{ __('Ativo') }}</span>
                    @else
                        <span class="shrink-0 rounded-full bg-zinc-500/10 px-2.5 py-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Inativo') }}</span>
                    @endif

                    <div class="flex shrink-0 items-center gap-1">
                        <flux:button
                            wire:click="abrirEdicao({{ $item->id }})"
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $item->nome"
                        />
                        <flux:button
                            wire:click="destroy({{ $item->id }})"
                            wire:confirm="{{ __('Remover este registro?') }}"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $item->nome"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon :icon="$abas[$aba][0]" class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhum registro cadastrado') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Crie registros para disponibilizá-los nos formulários de estações.') }}
                    </p>
                    <flux:button wire:click="abrirNovo" variant="primary" size="sm" icon="plus" class="mt-5">
                        {{ __('Cadastrar primeiro registro') }}
                    </flux:button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">{{ $itemId ? __('Editar registro') : __('Novo registro') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('O registro aparece nos formulários de estações. Registros inativos ficam ocultos na seleção.') }}
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                    <flux:input wire:model="nome" type="text" required placeholder="{{ __('Ex.: LTE') }}" />
                    <flux:error name="nome" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Descrição') }}</flux:label>
                    <flux:textarea wire:model="descricao" rows="2" placeholder="{{ __('Opcional') }}" />
                    <flux:error name="descricao" />
                </flux:field>

                <flux:switch wire:model="ativo" :label="__('Registro ativo')" />
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="check" wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar">
                    {{ __('Salvar') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>