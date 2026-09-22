<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Page header --}}
    <x-ui.page-header
        :title="__('Novo Projeto TIM')"
        :subtitle="__('Cadastre um novo projeto de implantação TIM')"
        :breadcrumbs="[
            ['label' => __('Projetos'), 'href' => null],
            ['label' => __('TIM'), 'href' => route('tim.index')],
            ['label' => __('Novo'), 'href' => null],
        ]"
    >
        <flux:button href="{{ route('tim.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Voltar') }}
        </flux:button>
    </x-ui.page-header>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        <form wire:submit="save" class="w-full space-y-6">
            {{-- Identificação --}}
            <section id="identificacao" data-section class="animate-fade-in-up scroll-mt-24">
                <x-ui.form-section
                    icon="identification"
                    :title="__('Identificação')"
                    :description="__('Informações básicas do projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Status') }} <span class="text-rose-500">*</span></flux:label>
                            <flux:select wire:model="status">
                                @foreach (\App\Models\TimProjeto::STATUS as $status)
                                    <flux:select.option :value="$status">{{ $status }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Código Personalizado') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="nome" type="text" required autofocus placeholder="{{ __('Ex.: Implantação RAN TIM') }}" />
                        <flux:error name="nome" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Descrição') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="3" placeholder="{{ __('Opcional') }}" />
                        <flux:error name="descricao" />
                    </flux:field>

                    <flux:field>

                        <div x-data="{ open: false }" class="relative">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between gap-2 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-left text-sm shadow-sm transition-colors hover:bg-zinc-50 dark:border-white/15 dark:bg-white/5 dark:hover:bg-white/10"
                                :class="open ? 'ring-2 ring-accent ring-offset-2' : ''"
                            >
                                @if ($this->estacaoSelecionada)
                                    <span class="flex min-w-0 items-center gap-2">
                                        <flux:icon.map-pin class="size-4 shrink-0 text-sky-500 dark:text-sky-400" />
                                        <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $this->estacaoSelecionada->site_id }}</span>
                                        <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $this->estacaoSelecionada->municipio ?: $this->estacaoSelecionada->endereco_id }}</span>
                                    </span>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">{{ __('Selecione uma estação...') }}</span>
                                @endif
                                <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute z-30 mt-1.5 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl shadow-black/5 dark:border-white/10 dark:bg-zinc-900"
                            >
                                <div class="border-b border-zinc-100 p-2 dark:border-white/5">
                                    <flux:input
                                        wire:model.live="buscaEstacao"
                                        :placeholder="__('Buscar por site ID, endereço ou município...')"
                                        icon="magnifying-glass"
                                        size="sm"
                                    />
                                </div>

                                <div class="flex max-h-64 flex-col divide-y divide-zinc-100 overflow-y-auto dark:divide-white/5">
                                    @if ($this->estacoesEncontradas->isNotEmpty())
                                        @foreach ($this->estacoesEncontradas as $estacao)
                                            <button
                                                type="button"
                                                wire:key="estacao-{{ $estacao->id }}"
                                                wire:click="selectEstacao({{ $estacao->id }})"
                                                @click="open = false"
                                                class="{{ (string) $estacao->id === $this->estacao_id ? 'bg-sky-50/60 dark:bg-sky-400/5' : '' }} flex w-full cursor-pointer items-center gap-3 px-3.5 py-2.5 text-left transition-colors hover:bg-zinc-50 dark:hover:bg-white/5"
                                            >
                                                <span class="{{ (string) $estacao->id === $this->estacao_id ? 'border-sky-500 bg-sky-500 text-white' : 'border-zinc-300 bg-white dark:border-white/20' }} flex size-5 shrink-0 items-center justify-center rounded-full border transition-colors">
                                                    @if ((string) $estacao->id === $this->estacao_id)
                                                        <flux:icon.check class="size-3.5" />
                                                    @endif
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="block truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $estacao->site_id }}</span>
                                                    <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $estacao->municipio ?: $estacao->endereco_id }}</span>
                                                </span>
                                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                                                    <flux:icon.map-pin class="size-3" />
                                                    {{ $estacao->regional ?: $estacao->estado }}
                                                </span>
                                            </button>
                                        @endforeach
                                    @else
                                        <p class="px-3.5 py-4 text-center text-sm text-zinc-400 dark:text-zinc-500">
                                            {{ __('Nenhuma estação encontrada.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <flux:error name="estacao_id" />
                    </flux:field>

                    <div class="rounded-2xl border border-sky-100 bg-sky-50/40 p-4 sm:p-5 dark:border-sky-400/15 dark:bg-sky-400/5">
                        <div class="mb-4 flex items-center gap-2.5">
                            <span class="inline-flex size-8 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-300">
                                <flux:icon.document-text class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Ordens de Serviço (FAM)') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('OC e códigos das OS de implantação') }}</p>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <flux:field class="sm:col-span-2">
                                <flux:label>{{ __('OC') }}</flux:label>
                                <flux:input wire:model="oc" type="text" :placeholder="__('Ordem complexa')" />
                                <flux:error name="oc" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('OS FAM Entrega') }}</flux:label>
                                <flux:input wire:model="os_fam_entrega" type="text" :placeholder="__('Ex.: 123-456-789')" />
                                <flux:error name="os_fam_entrega" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('OS FAM Instalação') }}</flux:label>
                                <flux:input wire:model="os_fam_instalacao" type="text" :placeholder="__('Ex.: 123-456-789')" />
                                <flux:error name="os_fam_instalacao" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('OS FAM Panorâmica') }}</flux:label>
                                <flux:input wire:model="os_fam_panoramica" type="text" :placeholder="__('Ex.: 123-456-789')" />
                                <flux:error name="os_fam_panoramica" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('OS FAM Desinstalação') }}</flux:label>
                                <flux:input wire:model="os_fam_desinstalacao" type="text" :placeholder="__('Ex.: 123-456-789')" />
                                <flux:error name="os_fam_desinstalacao" />
                            </flux:field>
                        </div>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 40ms">
                <x-ui.form-section
                    icon="calendar-days"
                    :title="__('Cronograma')"
                    :description="__('Datas previstas para o projeto')"
                >
                    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-2.5 border-b border-zinc-200 p-4 dark:border-white/10">
                            <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                <flux:icon.calendar-days class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Datas das etapas') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Baseline, planejada e real de cada etapa') }}</p>
                            </div>
                        </div>

                        @php
                            $etapasCronograma = [
                                'MOS' => ['baseline' => 'baseline_mos', 'planejada' => 'planejada_mos', 'real' => 'real_mos'],
                                'Instalação' => ['baseline' => 'baseline_instalacao', 'planejada' => 'planejada_instalacao', 'real' => 'real_instalacao'],
                                'Integração' => ['baseline' => 'baseline_integracao', 'planejada' => 'planejada_integracao', 'real' => 'real_integracao'],
                                'RFA' => ['baseline' => 'baseline_rfa', 'planejada' => 'planejada_rfa', 'real' => 'real_rfa'],
                            ];
                            $tiposData = ['Baseline' => 'baseline', 'Planejada' => 'planejada', 'Real' => 'real'];
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-zinc-200 bg-zinc-50/50 dark:border-white/10 dark:bg-white/[0.02]">
                                        <th class="w-32 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">{{ __('Data') }}</th>
                                        @foreach ($etapasCronograma as $etapa => $campos)
                                            <th class="px-3 py-3 font-semibold text-zinc-900 dark:text-white">{{ $etapa }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiposData as $rotulo => $tipo)
                                        <tr class="border-b border-zinc-100 last:border-0 dark:border-white/5">
                                            <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">{{ __($rotulo) }}</td>
                                            @foreach ($etapasCronograma as $campos)
                                                <td class="px-3 py-3">
                                                    <flux:input wire:model="{{ $campos[$tipo] }}" type="date" size="sm" />
                                                    <flux:error name="{{ $campos[$tipo] }}" />
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Anexos --}}
            <section id="anexos" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 80ms">
                <x-ui.form-section
                    icon="paper-clip"
                    :title="__('Anexos')"
                    :description="__('TSSR, DOC-D e notas fiscais do projeto')"
                >
                    <div class="grid gap-6 sm:grid-cols-2">
                        @php
                            $categoriasAnexos = [
                                ['key' => 'anexos_tssr', 'label' => __('TSSR'), 'description' => __('Termo de Serviço / Site Registration')],
                                ['key' => 'anexos_docd', 'label' => __('DOC-D'), 'description' => __('Documento de Design')],
                                ['key' => 'anexos_notas_fiscais', 'label' => __('Notas Fiscais'), 'description' => __('Faturas e notas emitidas')],
                            ];
                        @endphp

                        @foreach ($categoriasAnexos as $categoria)
                            <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                                        <flux:icon.document-text class="size-4" />
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $categoria['label'] }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $categoria['description'] }}</p>
                                    </div>
                                </div>

                                <flux:input wire:model="{{ $categoria['key'] }}" type="file" multiple />
                                <flux:error name="{{ $categoria['key'] }}.*" />

                                @if (count($this->{$categoria['key']}) > 0)
                                    <p class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon.check class="size-3.5 text-emerald-500" />
                                        {{ count($this->{$categoria['key']}) }} {{ __('arquivo(s) selecionado(s)') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>
            </section>

            {{-- Status --}}
            <section id="status" data-section class="animate-fade-in-up scroll-mt-24" style="animation-delay: 120ms">
                <x-ui.form-section
                    icon="flag"
                    :title="__('Situação')"
                    :description="__('Ative ou desative o projeto')"
                >
                    <flux:switch wire:model="ativo" :label="__('Projeto ativo')" />
                </x-ui.form-section>
            </section>

            {{-- Actions --}}
            <div class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md sm:flex-row sm:items-center sm:justify-between dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 120ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('tim.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Projeto') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar de seções (desktop) --}}
        <x-ui.form-nav :sections="[
            ['identificacao', 'identification', __('Identificação')],
            ['cronograma', 'calendar-days', __('Cronograma')],
            ['anexos', 'paper-clip', __('Anexos')],
            ['status', 'flag', __('Situação')],
        ]">
            <div class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Vincule as estações deste projeto. Cada estação pertence a apenas um projeto.') }}
                </p>
            </div>
        </x-ui.form-nav>
    </div>
</div>