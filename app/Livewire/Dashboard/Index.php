<?php

namespace App\Livewire\Dashboard;

use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\Estacao;
use App\Models\OrdemServico;
use App\Models\Produto;
use App\Models\RadioLink;
use App\Models\Servico;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Index extends Component
{
    /**
     * @return array{
     *     estacoes: int,
     *     emOperacao: int,
     *     cobertura: int,
     *     municipios: int,
     *     radioLinks: int,
     *     radioLinksAtivos: int,
     *     ordens: int,
     *     ordensAbertas: int,
     *     clientes: int,
     *     clientesAtivos: int,
     *     colaboradores: int,
     *     produtos: int,
     *     servicos: int,
     * }
     */
    #[Computed]
    public function stats(): array
    {
        $estacoes = Estacao::query()->count();
        $emOperacao = Estacao::query()->where('situacao', 'Em operação')->count();

        return [
            'estacoes' => $estacoes,
            'emOperacao' => $emOperacao,
            'cobertura' => $estacoes > 0 ? (int) round(($emOperacao / $estacoes) * 100) : 0,
            'municipios' => Estacao::query()->whereNotNull('municipio')->distinct()->count(),
            'radioLinks' => RadioLink::query()->count(),
            'radioLinksAtivos' => RadioLink::query()->where('status', 'Ativo')->count(),
            'ordens' => OrdemServico::query()->count(),
            'ordensAbertas' => OrdemServico::query()->whereIn('status', ['Aberta', 'Em andamento', 'Aguardando'])->count(),
            'clientes' => Cliente::query()->count(),
            'clientesAtivos' => Cliente::query()->where('ativo', true)->count(),
            'colaboradores' => Colaborador::query()->count(),
            'produtos' => Produto::query()->count(),
            'servicos' => Servico::query()->count(),
        ];
    }

    /**
     * Distribuição de estações por status.
     *
     * @return Collection<int, array{rotulo: string, total: int, percentual: int}>
     */
    #[Computed]
    public function estacoesPorStatus(): Collection
    {
        $contagem = Estacao::query()
            ->get('status')
            ->countBy(fn (Estacao $estacao) => $estacao->status ?: 'Sem status')
            ->sortDesc();

        return $this->proporcoes($contagem);
    }

    /**
     * Distribuição de estações por tecnologia.
     *
     * @return Collection<int, array{rotulo: string, total: int, percentual: int}>
     */
    #[Computed]
    public function estacoesPorTecnologia(): Collection
    {
        $contagem = Estacao::query()
            ->whereNotNull('tecnologia')
            ->get('tecnologia')
            ->countBy(fn (Estacao $estacao) => $estacao->tecnologia ?? 'Sem tecnologia')
            ->sortDesc();

        return $this->proporcoes($contagem);
    }

    /**
     * As estações mais recentemente atualizadas.
     *
     * @return Collection<int, Estacao>
     */
    #[Computed]
    public function ultimasEstacoes(): Collection
    {
        return Estacao::query()
            ->latest('updated_at')
            ->take(5)
            ->get(['id', 'site_id', 'municipio', 'estado', 'tecnologia', 'status', 'situacao', 'updated_at']);
    }

    /**
     * As ordens de serviço mais recentes.
     *
     * @return Collection<int, OrdemServico>
     */
    #[Computed]
    public function ultimasOrdens(): Collection
    {
        return OrdemServico::query()
            ->with('radioLink')
            ->latest('data_abertura')
            ->take(5)
            ->get(['id', 'codigo', 'titulo', 'status', 'prioridade', 'radio_link_id', 'data_abertura']);
    }

    /**
     * @return array<int, array{nome: string, descricao: string, rota: string, icone: string, contagem: int}>
     */
    #[Computed]
    public function modulos(): array
    {
        $stats = $this->stats();

        return [
            [
                'nome' => __('Estações'),
                'descricao' => __('Infraestrutura de telecom'),
                'rota' => route('estacoes.index'),
                'icone' => 'signal',
                'contagem' => $stats['estacoes'],
            ],
            [
                'nome' => __('Radio Links'),
                'descricao' => __('Enlaces de rádio'),
                'rota' => route('radio-links.index'),
                'icone' => 'radio',
                'contagem' => $stats['radioLinks'],
            ],
            [
                'nome' => __('Ordens de Serviço'),
                'descricao' => __('Manutenção e instalação'),
                'rota' => route('ordens-servico.index'),
                'icone' => 'clipboard-document-list',
                'contagem' => $stats['ordens'],
            ],
            [
                'nome' => __('Clientes'),
                'descricao' => __('Base de clientes'),
                'rota' => route('clientes.index'),
                'icone' => 'building-office',
                'contagem' => $stats['clientes'],
            ],
            [
                'nome' => __('Colaboradores'),
                'descricao' => __('Equipe e RH'),
                'rota' => route('colaboradores.index'),
                'icone' => 'users',
                'contagem' => $stats['colaboradores'],
            ],
            [
                'nome' => __('Produtos'),
                'descricao' => __('Equipamentos e insumos'),
                'rota' => route('produtos.index'),
                'icone' => 'cube',
                'contagem' => $stats['produtos'],
            ],
            [
                'nome' => __('Serviços'),
                'descricao' => __('Instalação, manutenção e suporte'),
                'rota' => route('servicos.index'),
                'icone' => 'wrench-screwdriver',
                'contagem' => $stats['servicos'],
            ],
        ];
    }

    /**
     * Saudação baseada no horário do dia.
     */
    public function saudacao(): string
    {
        $hora = (int) now()->format('G');

        return match (true) {
            $hora >= 5 && $hora < 12 => __('Bom dia'),
            $hora >= 12 && $hora < 18 => __('Boa tarde'),
            default => __('Boa noite'),
        };
    }

    /**
     * @param  Collection<string, int>  $contagem
     * @return Collection<int, array{rotulo: string, total: int, percentual: int}>
     */
    private function proporcoes(Collection $contagem): Collection
    {
        $total = $contagem->sum();

        return $contagem
            ->map(fn (int $quantidade, string $rotulo) => [
                'rotulo' => $rotulo,
                'total' => $quantidade,
                'percentual' => $total > 0 ? (int) ceil(($quantidade / $total) * 100) : 0,
            ])
            ->values();
    }

    public function render(): View
    {
        return view('livewire.dashboard.index');
    }
}
