<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
    <div class="relative mb-2 w-full">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('estacoes.index') }}" wire:navigate icon="arrow-left" variant="ghost" size="sm" />
            <div>
                <flux:heading size="xl" level="1">{{ $this->estacao->site_id }}</flux:heading>
                <flux:subheading size="lg">{{ __('Detalhes da estação') }}</flux:subheading>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:button href="{{ route('estacoes.edit', $this->estacao) }}" wire:navigate variant="primary" icon="pencil">
            {{ __('Editar') }}
        </flux:button>
        <flux:button wire:click="confirmDelete" variant="danger" icon="trash">
            {{ __('Excluir') }}
        </flux:button>
    </div>

    <flux:heading size="lg">{{ __('Identificação') }}</flux:heading>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de elemento') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_elemento ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tecnologia') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tecnologia ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de conexão') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_conexao ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Classificação') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->classificacao ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Endereço ID') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->endereco_id ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Station ID') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->station_id ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Ordem Complexa') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->ordem_complexa ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</flux:text>
            <div class="mt-1">
                @if ($this->estacao->status)
                    <flux:badge color="blue">{{ $this->estacao->status }}</flux:badge>
                @else
                    <flux:text>-</flux:text>
                @endif
            </div>
        </div>
    </div>

    <flux:heading size="lg" class="mt-2">{{ __('Datas') }}</flux:heading>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Aquisição') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->data_aquisicao?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Construção') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->data_construcao?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Ativação') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->data_ativacao?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Desativação') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->data_desativacao?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Cancelamento') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->data_cancelamento?->format('d/m/Y') ?? '-' }}</flux:text>
        </div>
    </div>

    <flux:heading size="lg" class="mt-2">{{ __('Contratos e infraestrutura') }}</flux:heading>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de contrato da Área') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_contrato_area ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Detentor da Área') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->detentor_area ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de contrato Infra') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_contrato_infra ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Detentor de Infra') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->detentor_infra ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de Infra') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_infra ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo de EV') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_ev ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Fornecedor de EV') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->fornecedor_ev ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Tipo da torre') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->tipo_torre ?? '-' }}</flux:text>
        </div>
    </div>

    <flux:heading size="lg" class="mt-2">{{ __('Endereço') }}</flux:heading>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Logradouro') }}</flux:text>
            <flux:text class="mt-1 block">
                {{ trim(($this->estacao->tipo_logradouro ?? '').' '.($this->estacao->logradouro ?? '')) ?: '-' }}
                {{ $this->estacao->numero ? ', '.$this->estacao->numero : '' }}
            </flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Complemento') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->complemento ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Bairro') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->bairro ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Município') }}</flux:text>
            <flux:text class="mt-1 block">
                {{ $this->estacao->municipio ?? '-' }}{{ $this->estacao->estado ? ' - '.$this->estacao->estado : '' }}
            </flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('CEP') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->cep ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Regional') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->regional ?? '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Latitude') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->latitude !== null ? number_format((float) $this->estacao->latitude, 6, ',', '.') : '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Longitude') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->longitude !== null ? number_format((float) $this->estacao->longitude, 6, ',', '.') : '-' }}</flux:text>
        </div>
    </div>

    <flux:heading size="lg" class="mt-2">{{ __('Estrutura') }}</flux:heading>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('AEV Nominal') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->aev_nominal !== null ? number_format((float) $this->estacao->aev_nominal, 2, ',', '.') : '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Área de solo (m²)') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->area_solo !== null ? number_format((float) $this->estacao->area_solo, 2, ',', '.') : '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Altura da estrutura (m)') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->altura_estrutura !== null ? number_format((float) $this->estacao->altura_estrutura, 2, ',', '.') : '-' }}</flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('OTs') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->ots ?? '-' }}</flux:text>
        </div>
    </div>

    @if ($this->estacao->observacao)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Observação') }}</flux:text>
            <flux:text class="mt-1 block whitespace-pre-line">{{ $this->estacao->observacao }}</flux:text>
        </div>
    @endif

    @if ($this->estacao->justificativa)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Justificativa') }}</flux:text>
            <flux:text class="mt-1 block whitespace-pre-line">{{ $this->estacao->justificativa }}</flux:text>
        </div>
    @endif

    @if ($this->estacao->observacao_thq)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Observação THQ') }}</flux:text>
            <flux:text class="mt-1 block whitespace-pre-line">{{ $this->estacao->observacao_thq }}</flux:text>
        </div>
    @endif

    @if ($this->estacao->situacao)
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:text class="text-xs font-medium uppercase text-neutral-500 dark:text-neutral-400">{{ __('Situação') }}</flux:text>
            <flux:text class="mt-1 block">{{ $this->estacao->situacao }}</flux:text>
        </div>
    @endif
</div>

@if ($showDeleteModal)
    <flux:modal wire:model="showDeleteModal">
        <flux:heading level="2">{{ __('Excluir estação') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Tem certeza que deseja excluir') }} <strong>{{ $this->estacao->site_id }}</strong>? {{ __('Esta ação não pode ser desfeita.') }}
        </flux:text>
        <div class="flex justify-end gap-2 pt-2">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="destroy">{{ __('Excluir') }}</flux:button>
        </div>
    </flux:modal>
@endif