<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Editar Produto')"
        :subtitle="__('Atualize os dados de') . ' ' . $this->produto->nome"
        :breadcrumbs="[
            ['label' => __('Gestão'), 'href' => null],
            ['label' => __('Produtos'), 'href' => route('produtos.index')],
            ['label' => __('Editar'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('produtos.show', $this->produto) }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Dados do produto --}}
            <section id="dados" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="cube"
                    :title="__('Dados do produto')"
                    :description="__('Informações de identificação do produto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Nome') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:input wire:model="nome" type="text" required autofocus />
                            <flux:error name="nome" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Código / SKU') }}</flux:label>
                            <flux:input wire:model="codigo" type="text" placeholder="PROD-0001" />
                            <flux:error name="codigo" />
                        </flux:field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Categoria') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="categoria" required>
                                <flux:select.option value="">{{ __('Selecione uma categoria') }}</flux:select.option>
                                @foreach ($categorias as $categoria)
                                    <flux:select.option :value="$categoria">{{ $categoria }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="categoria" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Preço') }}</flux:label>
                            <flux:input wire:model="preco" type="number" step="0.01" min="0" inputmode="decimal" placeholder="0,00" />
                            <flux:error name="preco" />
                        </flux:field>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Descrição --}}
            <section id="descricao" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 60ms">
                <x-ui.form-section
                    icon="document-text"
                    :title="__('Descrição')"
                    :description="__('Detalhes sobre o produto')"
                >
                    <flux:field>
                        <flux:label>{{ __('Descrição') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="3" />
                        <flux:error name="descricao" />
                    </flux:field>
                </x-ui.form-section>
            </section>

            {{-- Informações adicionais --}}
            <section id="informacoes" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="chat-bubble-left-right"
                    :title="__('Informações adicionais')"
                    :description="__('Observações e status do produto')"
                >
                    <flux:field>
                        <flux:label>{{ __('Observações') }}</flux:label>
                        <flux:textarea wire:model="observacoes" rows="3" />
                        <flux:error name="observacoes" />
                    </flux:field>

                    <flux:switch wire:model="ativo" :label="__('Produto ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 180ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('produtos.show', $this->produto) }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Produto') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['dados', 'cube', __('Dados do produto')],
            ['descricao', 'document-text', __('Descrição')],
            ['informacoes', 'chat-bubble-left-right', __('Informações adicionais')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('O código do produto deve ser único no sistema.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>