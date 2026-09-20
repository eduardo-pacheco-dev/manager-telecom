<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('clientes.index') }}" wire:navigate icon="arrow-left" variant="ghost" size="sm" />
            <div>
                <flux:heading size="xl" level="1">{{ $this->cliente->nome }}</flux:heading>
                <flux:subheading size="lg">{{ __('Detalhes do cliente') }}</flux:subheading>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:button href="{{ route('clientes.edit', $this->cliente) }}" wire:navigate variant="primary" icon="pencil">
            {{ __('Editar') }}
        </flux:button>
        <flux:button wire:click="toggleAtivo" variant="ghost" icon="{{ $this->cliente->ativo ? 'x-mark' : 'check' }}">
            {{ $this->cliente->ativo ? __('Desativar') : __('Ativar') }}
        </flux:button>
        <flux:button wire:click="confirmDelete" variant="danger" icon="trash">
            {{ __('Excluir') }}
        </flux:button>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->email }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('CNPJ') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->documento }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Telefone') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->telefone ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Segmento') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->segmento ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Cidade / UF') }}</flux:text>
            <flux:text class="mt-1 block">{{ trim(($this->cliente->cidade ?? '').($this->cliente->estado ? ' - '.$this->cliente->estado : '')) ?: '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</flux:text>
            <div class="mt-1">
                @if ($this->cliente->ativo)
                    <flux:badge color="green">{{ __('Ativo') }}</flux:badge>
                @else
                    <flux:badge color="red">{{ __('Inativo') }}</flux:badge>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('CEP') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->cep ?? '-' }}</flux:text>
        </div>
    </div>

    @if ($this->cliente->endereco)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Endereço') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->cliente->endereco }}</flux:text>
        </div>
    @endif

    @if ($this->cliente->observacoes)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Observações') }}</flux:text>
            <flux:text class="mt-1 block whitespace-pre-line">{{ $this->cliente->observacoes }}</flux:text>
        </div>
    @endif
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir cliente') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->cliente->nome }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif