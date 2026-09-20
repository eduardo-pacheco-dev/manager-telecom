<div class="flex h-full w-full flex-1 flex-col gap-6 p-4 sm:p-6">
    {{-- Hero / Page header --}}
    <div class="animate-fade-in-up relative overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-500/5 via-transparent to-emerald-500/5 dark:from-sky-400/10 dark:via-transparent dark:to-emerald-400/10"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 size-48 rounded-full bg-sky-400/10 blur-3xl dark:bg-sky-400/15"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 size-56 rounded-full bg-emerald-400/10 blur-3xl dark:bg-emerald-400/15"></div>

        <div class="relative flex flex-col gap-5 p-5 sm:p-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('ordens-servico.index')" wire:navigate>{{ __('Ordens de Serviço') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item class="text-zinc-900 dark:text-white">{{ __('Nova Ordem') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <flux:heading size="xl" level="1">{{ __('Nova Ordem de Serviço') }}</flux:heading>
                    <flux:subheading size="lg" class="mt-1">{{ __('Preencha os dados para cadastrar uma nova ordem') }}</flux:subheading>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                        {{ __('Voltar') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
        {{-- Form --}}
        <form wire:submit="save" class="flex flex-col gap-6">
            @php
                $linkSelecionado = $radioLinks->firstWhere('id', (int) $radio_link_id);
            @endphp

            {{-- Identificação --}}
            <section id="identificacao" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 40ms">
                <div class="flex items-center gap-3 border-b border-zinc-200 px-5 py-4 dark:border-white/10">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400">
                        <flux:icon.identification class="size-4.5" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Identificação') }}</flux:heading>
                        <flux:subheading size="sm">{{ __('Dados básicos da ordem de serviço') }}</flux:subheading>
                    </div>
                </div>

                <div class="space-y-6 p-5">
                    <div class="grid gap-6 sm:grid-cols-2">
<flux:field>
                        <flux:label>{{ __('Código') }} <span class="text-rose-500">*</span></flux:label>
                        <div class="flex items-center gap-2">
                            <flux:input wire:model="codigo" type="text" readonly required class="flex-1 cursor-default bg-zinc-50 text-zinc-600 dark:bg-white/5 dark:text-zinc-300" />
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                                <flux:icon.sparkles class="size-3.5" />
                                {{ __('Automático') }}
                            </span>
                        </div>
                        <flux:error name="codigo" />
                    </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Tipo') }}</flux:label>
                            <flux:select wire:model="tipo">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach (\App\Models\OrdemServico::TIPOS as $tipo)
                                    <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="tipo" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Título') }} <span class="text-rose-500">*</span></flux:label>
                        <flux:input wire:model="titulo" type="text" required placeholder="{{ __('Ex.: Manutenção preventiva no link principal') }}" />
                        <flux:error name="titulo" />
                    </flux:field>
                </div>
            </section>

            {{-- Enlace --}}
            <section id="enlace" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 80ms">
                <div class="flex items-center gap-3 border-b border-zinc-200 px-5 py-4 dark:border-white/10">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                        <flux:icon.radio class="size-4.5" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Enlace e responsáveis') }}</flux:heading>
                        <flux:subheading size="sm">{{ __('Vincule o radio link e as estações A/B') }}</flux:subheading>
                    </div>
                </div>

                <div class="space-y-6 p-5">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <flux:field>
                            <flux:label>{{ __('Radio Link') }}</flux:label>
                            <flux:select wire:model="radio_link_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($radioLinks as $radioLink)
                                    <flux:select.option :value="$radioLink->id">{{ $radioLink->codigo }} · {{ $radioLink->estacaoA->site_id }} → {{ $radioLink->estacaoB->site_id }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="radio_link_id" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Responsável') }}</flux:label>
                            <flux:select wire:model="responsavel_id">
                                <flux:select.option value="">{{ __('Selecione...') }}</flux:select.option>
                                @foreach ($responsaveis as $responsavel)
                                    <flux:select.option :value="$responsavel->id">{{ $responsavel->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="responsavel_id" />
                        </flux:field>
                    </div>

                    {{-- Link selecionado: visual A → B --}}
                    @if ($linkSelecionado)
                        <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                            <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                    <flux:icon.map-pin class="size-4.5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação A') }}</p>
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoA->site_id }}</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center justify-center gap-2 px-1">
                                <flux:icon.arrow-right class="size-4 text-violet-500 dark:text-violet-400" />
                                <span class="rounded-full bg-violet-500/10 px-2.5 py-0.5 text-xs font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">{{ $linkSelecionado->codigo }}</span>
                            </div>

                            <div class="flex min-w-0 flex-1 items-center gap-3 rounded-xl border border-violet-200 bg-violet-50/60 px-4 py-3 dark:border-violet-400/20 dark:bg-violet-400/10">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10 text-violet-600 dark:bg-violet-400/10 dark:text-violet-400">
                                    <flux:icon.map-pin class="size-4.5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ __('Estação B') }}</p>
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $linkSelecionado->estacaoB->site_id }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3 rounded-xl border border-dashed border-zinc-200 bg-zinc-50/60 px-4 py-3 dark:border-white/10 dark:bg-white/[0.02]">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-white/10 dark:text-zinc-500">
                                <flux:icon.sparkles class="size-4.5" />
                            </div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Selecione um radio link para preencher as estações A/B automaticamente.') }}
                            </p>
                        </div>
                    @endif

                    {{-- Estações (hidden fields) --}}
                    <input type="hidden" wire:model="estacao_a_id" />
                    <input type="hidden" wire:model="estacao_b_id" />
                </div>
            </section>

            {{-- Status e prioridade --}}
            <section id="status-prioridade" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 120ms">
                <div class="flex items-center gap-3 border-b border-zinc-200 px-5 py-4 dark:border-white/10">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                        <flux:icon.flag class="size-4.5" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Status e prioridade') }}</flux:heading>
                        <flux:subheading size="sm">{{ __('Defina a situação atual e a urgência da ordem') }}</flux:subheading>
                    </div>
                </div>

                <div class="space-y-6 p-5">
                    <flux:field>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <flux:radio.group variant="cards" wire:model="status" class="flex-wrap">
                            <flux:radio variant="cards" value="Aberta" icon="clock" label="{{ __('Aberta') }}" description="{{ __('Ordem criada, aguardando início') }}" />
                            <flux:radio variant="cards" value="Em andamento" icon="wrench" label="{{ __('Em andamento') }}" description="{{ __('Trabalho em execução no campo') }}" />
                            <flux:radio variant="cards" value="Aguardando" icon="queue-list" label="{{ __('Aguardando') }}" description="{{ __('Aguardando recurso ou liberação') }}" />
                            <flux:radio variant="cards" value="Concluída" icon="check-circle" label="{{ __('Concluída') }}" description="{{ __('Serviço finalizado') }}" />
                            <flux:radio variant="cards" value="Cancelada" icon="x-circle" label="{{ __('Cancelada') }}" description="{{ __('Ordem não realizada') }}" />
                        </flux:radio.group>
                        <flux:error name="status" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Prioridade') }}</flux:label>
                        <flux:radio.group variant="cards" wire:model="prioridade" class="flex-wrap">
                            <flux:radio variant="cards" value="Baixa" icon="arrow-down" label="{{ __('Baixa') }}" description="{{ __('Pode ser agendada sem pressa') }}" />
                            <flux:radio variant="cards" value="Média" icon="chart-bar" label="{{ __('Média') }}" description="{{ __('Prioridade padrão de atendimento') }}" />
                            <flux:radio variant="cards" value="Alta" icon="arrow-up" label="{{ __('Alta') }}" description="{{ __('Requer atenção no mesmo dia') }}" />
                            <flux:radio variant="cards" value="Urgente" icon="bolt" label="{{ __('Urgente') }}" description="{{ __('Atendimento imediato') }}" />
                        </flux:radio.group>
                        <flux:error name="prioridade" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Solicitante') }}</flux:label>
                        <flux:input wire:model="solicitante" type="text" placeholder="{{ __('Ex.: NOC') }}" icon="users" />
                        <flux:error name="solicitante" />
                    </flux:field>
                </div>
            </section>

            {{-- Cronograma --}}
            <section id="cronograma" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 160ms">
                <div class="flex items-center gap-3 border-b border-zinc-200 px-5 py-4 dark:border-white/10">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                        <flux:icon.calendar-days class="size-4.5" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Cronograma') }}</flux:heading>
                        <flux:subheading size="sm">{{ __('Datas previstas para a execução') }}</flux:subheading>
                    </div>
                </div>

                <div class="grid gap-6 p-5 sm:grid-cols-3">
                    <flux:field>
                        <flux:label>{{ __('Data de abertura') }}</flux:label>
                        <flux:input wire:model="data_abertura" type="date" />
                        <flux:error name="data_abertura" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Data de agendamento') }}</flux:label>
                        <flux:input wire:model="data_agendamento" type="date" />
                        <flux:error name="data_agendamento" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Data de conclusão') }}</flux:label>
                        <flux:input wire:model="data_conclusao" type="date" />
                        <flux:error name="data_conclusao" />
                    </flux:field>
                </div>
            </section>

            {{-- Descrição --}}
            <section id="descricao" class="animate-fade-in-up overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]" style="animation-delay: 200ms">
                <div class="flex items-center gap-3 border-b border-zinc-200 px-5 py-4 dark:border-white/10">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-400/10 dark:text-rose-400">
                        <flux:icon.document-text class="size-4.5" />
                    </div>
                    <div>
                        <flux:heading size="lg">{{ __('Descrição') }}</flux:heading>
                        <flux:subheading size="sm">{{ __('Detalhes e instruções do serviço') }}</flux:subheading>
                    </div>
                </div>

                <div class="p-5">
                    <flux:field>
                        <flux:label>{{ __('Descrição do serviço') }}</flux:label>
                        <flux:textarea wire:model="descricao" rows="4" placeholder="{{ __('Descreva o escopo, equipamentos e instruções da ordem...') }}" />
                        <flux:error name="descricao" />
                    </flux:field>
                </div>
            </section>

            {{-- Ações --}}
            <div class="animate-fade-in-up flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-zinc-200 bg-white/90 p-4 backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/90" style="animation-delay: 240ms">
                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    {{ __('Campos marcados com') }} <span class="text-rose-500">*</span> {{ __('são obrigatórios.') }}
                </p>
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('ordens-servico.index') }}" wire:navigate variant="filled">{{ __('Cancelar') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check" wire:loading.attr="disabled" wire:target="save">
                        {{ __('Salvar Ordem') }}
                    </flux:button>
                </div>
            </div>
        </form>

        {{-- Sidebar (desktop) --}}
        <aside
            class="sticky top-4 hidden animate-fade-in-up lg:block"
            style="animation-delay: 260ms"
            x-data="{
                active: 'identificacao',
                sections: ['identificacao', 'enlace', 'status-prioridade', 'cronograma', 'descricao'],
                init() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) this.active = entry.target.id;
                        });
                    }, { rootMargin: '-25% 0px -65% 0px' });
                    this.sections.forEach((id) => {
                        const el = document.getElementById(id);
                        if (el) observer.observe(el);
                    });
                },
                go(id) {
                    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                },
            }"
        >
            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-white/10 dark:bg-white/[0.03]">
                <div class="border-b border-zinc-200 px-4 py-3.5 dark:border-white/10">
                    <p class="flex items-center gap-2 text-sm font-medium text-zinc-900 dark:text-white">
                        <flux:icon.queue-list class="size-4 text-sky-500 dark:text-sky-400" />
                        {{ __('Seções do formulário') }}
                    </p>
                </div>

                <nav class="p-2">
                    @php
                        $navItems = [
                            'identificacao' => ['identification', __('Identificação')],
                            'enlace' => ['radio', __('Enlace e responsáveis')],
                            'status-prioridade' => ['flag', __('Status e prioridade')],
                            'cronograma' => ['calendar-days', __('Cronograma')],
                            'descricao' => ['document-text', __('Descrição')],
                        ];
                    @endphp

                    @foreach ($navItems as $id => [$icon, $label])
                        <button
                            type="button"
                            @click="go('{{ $id }}')"
                            class="group flex w-full cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-sm transition-colors"
                            :class="active === '{{ $id }}'
                                ? 'bg-sky-500/10 font-medium text-sky-700 dark:bg-sky-400/10 dark:text-sky-300'
                                : 'text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-white'"
                        >
                            <span
                                class="flex size-7 shrink-0 items-center justify-center rounded-md transition-colors"
                                :class="active === '{{ $id }}'
                                    ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/30'
                                    : 'bg-zinc-100 text-zinc-400 group-hover:bg-zinc-200 dark:bg-white/10 dark:text-zinc-500 dark:group-hover:bg-white/20'"
                            >
                                <flux:icon :icon="$icon" class="size-3.5" />
                            </span>
                            <span class="truncate">{{ $label }}</span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="mt-4 rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-400/20 dark:bg-sky-400/10">
                <p class="flex items-center gap-2 text-sm font-medium text-sky-700 dark:text-sky-300">
                    <flux:icon.light-bulb class="size-4" />
                    {{ __('Dica') }}
                </p>
                <p class="mt-1.5 text-xs leading-relaxed text-sky-800/80 dark:text-sky-200/70">
                    {{ __('Ao selecionar um radio link, as estações A e B são preenchidas automaticamente. Você pode usar o status e a prioridade para priorizar o atendimento.') }}
                </p>
            </div>
        </aside>
    </div>
</div>