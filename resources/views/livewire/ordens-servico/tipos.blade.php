<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Hero / Page header --}}
    <div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-emerald-500/5 dark:from-sky-400/10 dark:via-transparent dark:to-emerald-400/10"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 size-56 rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-400/15"></div>

        <div class="relative flex flex-col gap-5 p-5 sm:p-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('ordens-servico.index')" wire:navigate>{{ __('Ordens de Serviço') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Tipos') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <flux:heading size="xl" level="1">{{ __('Tipos de Ordem de Serviço') }}</flux:heading>
                    <flux:subheading size="lg" class="mt-1">{{ __('Gerencie os tipos disponíveis nos formulários de ordem de serviço') }}</flux:subheading>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                        {{ __('Voltar') }}
                    </flux:button>
                    <flux:button wire:click="abrirNovo" variant="primary" icon="plus">
                        {{ __('Novo tipo') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-3" aria-label="{{ __('Resumo') }}">
        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 40ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-zinc-200/40 blur-2xl dark:bg-white/5"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total de tipos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $this->tipos->count() }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-600 transition-transform group-hover:scale-105 dark:bg-white/10 dark:text-zinc-200">
                    <flux:icon.tag class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 90ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-emerald-200/40 blur-2xl dark:bg-emerald-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Ativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $this->tipos->where('ativo', true)->count() }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 transition-transform group-hover:scale-105 dark:bg-emerald-400/10 dark:text-emerald-400">
                    <flux:icon.check-circle class="size-5" />
                </div>
            </div>
        </div>

        <div class="animate-fade-in-up group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-white/10 dark:bg-white/5" style="animation-delay: 140ms">
            <div class="pointer-events-none absolute -right-8 -top-10 size-28 rounded-full bg-rose-200/40 blur-2xl dark:bg-rose-400/10"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ __('Inativos') }}</p>
                    <p class="mt-1.5 text-3xl font-semibold tracking-tight text-rose-600 dark:text-rose-400">{{ $this->tipos->where('ativo', false)->count() }}</p>
                </div>
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 transition-transform group-hover:scale-105 dark:bg-rose-400/10 dark:text-rose-400">
                    <flux:icon.x-circle class="size-5" />
                </div>
            </div>
        </div>
    </section>

    {{-- List --}}
    <div class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 180ms">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-white/10">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Tipos de ordem de serviço cadastrados') }}
            </p>
        </div>

        <div class="flex flex-col">
            @forelse ($this->tipos as $tipo)
                <div wire:key="tipo-{{ $tipo->id }}" class="group flex items-center gap-4 border-b border-zinc-100 px-5 py-4 transition-colors last:border-b-0 hover:bg-zinc-50/80 dark:border-white/5 dark:hover:bg-white/[0.02]">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl shadow-sm ring-1 ring-black/5 {{ $tipo->ativo ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300' : 'bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500' }}">
                            <flux:icon.tag class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium {{ $tipo->ativo ? 'text-zinc-900 dark:text-white' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $tipo->nome }}</p>
                            @if ($tipo->descricao)
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $tipo->descricao }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($tipo->ativo)
                        <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">{{ __('Ativo') }}</span>
                    @else
                        <span class="shrink-0 rounded-full bg-zinc-500/10 px-2.5 py-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Inativo') }}</span>
                    @endif

                    <div class="flex shrink-0 items-center gap-1">
                        <flux:button
                            wire:click="abrirEdicao({{ $tipo->id }})"
                            size="sm"
                            variant="ghost"
                            icon="pencil-square"
                            :title="__('Editar')"
                            :aria-label="__('Editar') . ' ' . $tipo->nome"
                        />
                        <flux:button
                            wire:click="destroy({{ $tipo->id }})"
                            size="sm"
                            variant="ghost"
                            icon="trash"
                            :title="__('Excluir')"
                            :aria-label="__('Excluir') . ' ' . $tipo->nome"
                        />
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-white/10">
                        <flux:icon.tag class="size-7 text-zinc-400 dark:text-zinc-500" />
                    </div>
                    <p class="mt-4 text-sm font-medium text-zinc-900 dark:text-white">
                        {{ __('Nenhum tipo cadastrado') }}
                    </p>
                    <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Crie tipos para categorizar as ordens de serviço nos formulários.') }}
                    </p>
                    <flux:button wire:click="abrirNovo" variant="primary" size="sm" icon="plus" class="mt-5">
                        {{ __('Cadastrar primeiro tipo') }}
                    </flux:button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <div class="space-y-5">
            <div>
                <flux:heading level="2">{{ $tipoId ? __('Editar tipo') : __('Novo tipo') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('O tipo aparece no formulário de ordem de serviço. Tipos inativos ficam ocultos na seleção.') }}
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                    <flux:input wire:model="nome" type="text" required placeholder="{{ __('Ex.: Manutenção') }}" />
                    <flux:error name="nome" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Descrição') }}</flux:label>
                    <flux:textarea wire:model="descricao" rows="2" placeholder="{{ __('Opcional') }}" />
                    <flux:error name="descricao" />
                </flux:field>

                <flux:switch wire:model="ativo" :label="__('Tipo ativo')" />
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