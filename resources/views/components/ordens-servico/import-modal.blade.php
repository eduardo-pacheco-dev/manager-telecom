@props(['showImportModal', 'importArquivo' => null])

<flux:modal wire:model="showImportModal" class="max-w-2xl">
    <div class="space-y-5">
        <div>
            <flux:heading level="2">{{ __('Importar ordens de serviço') }}</flux:heading>
            <flux:text class="mt-2">
                {{ __('Envie um arquivo Excel (.xlsx) ou CSV com as ordens de serviço. A importação roda em segundo plano via fila de jobs, ideal para arquivos com milhares de linhas.') }}
            </flux:text>
        </div>

        {{-- Dropzone --}}
        <div
            x-data="{ dragging: false }"
            @dragover.prevent="dragging = true"
            @dragenter.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="
                dragging = false;
                const files = $event.dataTransfer.files;
                if (files.length) $wire.upload('import_arquivo', files[0]);
            "
            class="group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed transition-all duration-200"
            :class="dragging
                ? 'border-sky-400 bg-sky-50/70 dark:border-sky-500 dark:bg-sky-400/10'
                : 'border-zinc-300 bg-zinc-50/50 hover:border-sky-300 hover:bg-sky-50/40 dark:border-white/15 dark:bg-white/[0.03] dark:hover:border-sky-500/50 dark:hover:bg-sky-400/5'"
            @click="$refs.fileInput.click()"
        >
            <input
                type="file"
                wire:model="import_arquivo"
                accept=".xlsx,.csv"
                class="sr-only"
                x-ref="fileInput"
            />

            <div class="flex flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                <div
                    class="flex size-14 items-center justify-center rounded-2xl transition-colors duration-200"
                    :class="dragging
                        ? 'bg-sky-500 text-white shadow-lg shadow-sky-500/30'
                        : 'bg-sky-500/10 text-sky-600 group-hover:bg-sky-500/15 dark:bg-sky-400/10 dark:text-sky-400'"
                >
                    <template x-if="!@js($importArquivo !== null)">
                        <flux:icon.arrow-up-tray class="size-7" />
                    </template>
                    <template x-if="@js($importArquivo !== null)">
                        <flux:icon.document-check class="size-7" />
                    </template>
                </div>

                <div class="space-y-1">
                    <p
                        class="text-sm font-semibold text-zinc-900 dark:text-white"
                        x-show="@js($importArquivo === null)"
                    >
                        {{ __('Arraste o arquivo aqui') }}
                    </p>
                    <p
                        class="truncate text-sm font-semibold text-zinc-900 dark:text-white"
                        x-show="@js($importArquivo !== null)"
                    >
                        @if ($importArquivo !== null)
                            {{ $importArquivo->getClientOriginalName() }}
                        @endif
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        <span x-show="@js($importArquivo === null)">
                            {{ __('ou clique para selecionar') }} · <strong>.xlsx</strong> / <strong>.csv</strong> · {{ __('até 200 MB') }}
                        </span>
                        <span x-show="@js($importArquivo !== null)">
                            {{ __('Arquivo selecionado. Clique para trocar.') }}
                        </span>
                    </p>
                </div>

                <flux:button
                    as="button"
                    type="button"
                    variant="subtle"
                    size="sm"
                    class="pointer-events-none"
                >
                    <flux:icon.folder class="size-4" />
                    {{ __('Selecionar arquivo') }}
                </flux:button>
            </div>
        </div>

        <flux:error name="import_arquivo" />

        {{-- Arquivo selecionado --}}
        @if ($importArquivo !== null)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-4 py-3 dark:border-emerald-400/20 dark:bg-emerald-400/10">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400">
                        <flux:icon.document-check class="size-4.5" />
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $importArquivo->getClientOriginalName() }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ number_format($importArquivo->getSize() / 1048576, 1, ',', '.') }} MB
                        </p>
                    </div>
                </div>
                <flux:button
                    wire:click="limparArquivoImportacao"
                    variant="ghost"
                    icon="x-mark"
                    size="sm"
                    :aria-label="__('Remover arquivo')"
                />
            </div>
        @endif

        {{-- Colunas esperadas --}}
        <div class="rounded-xl border border-zinc-100 bg-zinc-50/60 p-4 dark:border-white/5 dark:bg-white/[0.02]">
            <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300">{{ __('Colunas reconhecidas') }}:</p>
            <div class="mt-1.5 flex flex-wrap gap-1.5">
                @foreach (['Cód_AFL', 'Status_Geral', 'Site_ID A', 'Site_ID B', 'END_ID A', 'END_ID B', 'Projeto', 'Descrição', 'Supervisor', 'Coordenador', 'OC (TIM)', 'Chave_MW', 'SMP_Nokia', 'OBS GERAL', 'Data_Cadastro_Ativ'] as $coluna)
                    <span class="rounded-md bg-zinc-100 px-1.5 py-0.5 font-mono text-[11px] text-zinc-600 dark:bg-white/10 dark:text-zinc-300">{{ $coluna }}</span>
                @endforeach
            </div>
            <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                {{ __('Obrigatória') }}: <strong>Cód_AFL</strong>.
                {{ __('As estações A/B são resolvidas pelo Site ID; linhas sem código ou sem estação correspondente são ignoradas.') }}
            </p>
        </div>

        <div class="flex justify-end gap-2 pt-1">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button
                variant="primary"
                icon="arrow-up-tray"
                wire:click="iniciarImportacao"
                wire:loading.attr="disabled"
                wire:target="iniciarImportacao"
            >
                {{ __('Iniciar importação') }}
            </flux:button>
        </div>
    </div>
</flux:modal>