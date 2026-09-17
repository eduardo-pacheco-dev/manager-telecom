<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('colaboradores.index') }}" wire:navigate icon="arrow-left" variant="ghost" size="sm" />
            <div>
                <flux:heading size="xl" level="1">{{ $this->colaborador->nome }}</flux:heading>
                <flux:subheading size="lg">{{ __('Detalhes do colaborador') }}</flux:subheading>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:button href="{{ route('colaboradores.edit', $this->colaborador) }}" wire:navigate variant="primary" icon="pencil">
            {{ __('Editar') }}
        </flux:button>
        <flux:button wire:click="toggleAtivo" variant="ghost" icon="{{ $this->colaborador->ativo ? 'x-mark' : 'check' }}">
            {{ $this->colaborador->ativo ? __('Desativar') : __('Ativar') }}
        </flux:button>
        <flux:button wire:click="confirmDelete" variant="danger" icon="trash">
            {{ __('Excluir') }}
        </flux:button>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->email }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('CPF') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->cpf }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Telefone') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->telefone ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Cargo') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->cargo ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Departamento') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->departamento ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</flux:text>
            <div class="mt-1">
                @if ($this->colaborador->ativo)
                    <flux:badge color="green">{{ __('Ativo') }}</flux:badge>
                @else
                    <flux:badge color="red">{{ __('Inativo') }}</flux:badge>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Data de admissão') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->data_admissao?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Salário') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->salario ? 'R$ ' . number_format($this->colaborador->salario, 2, ',', '.') : '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('CEP') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->cep ?? '-' }}</flux:text>
        </div>
    </div>

    @if ($this->colaborador->endereco)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Endereço') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->colaborador->endereco }}{{ $this->colaborador->cidade ? ', ' . $this->colaborador->cidade : '' }}{{ $this->colaborador->estado ? ' - ' . $this->colaborador->estado : '' }}</flux:text>
        </div>
    @endif

    @if ($this->colaborador->observacoes)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Observações') }}</flux:text>
            <flux:text class="mt-1 block whitespace-pre-line">{{ $this->colaborador->observacoes }}</flux:text>
        </div>
    @endif
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir colaborador') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->colaborador->nome }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif
