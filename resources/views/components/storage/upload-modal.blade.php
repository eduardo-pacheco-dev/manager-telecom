@props(['showUploadModal', 'ordemServicoId', 'radioLinkId'])

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