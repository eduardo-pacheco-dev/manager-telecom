<?php

namespace App\Livewire\Dashboard;

use App\Models\Colaborador;
use App\Models\Estacao;
use App\Models\Produto;
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
     *     colaboradores: int,
     *     colaboradoresAtivos: int,
     *     produtos: int,
     *     produtosAtivos: int,
     *     servicos: int,
     *     servicosAtivos: int,
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
            'colaboradores' => Colaborador::query()->count(),
            'colaboradoresAtivos' => Colaborador::query()->where('ativo', true)->count(),
            'produtos' => Produto::query()->count(),
            'produtosAtivos' => Produto::query()->where('ativo', true)->count(),
            'servicos' => Servico::query()->count(),
            'servicosAtivos' => Servico::query()->where('ativo', true)->count(),
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
            ->countBy(fn (Estacao $estacao) => $estacao->tecnologia)
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
     * @return array<int, array{nome: string, descricao: string, rota: string, icone: string, contagem: int}>
     */
    #[Computed]
    public function modulos(): array
    {
        $stats = $this->stats;

        return [
            [
                'nome' => __('Estações'),
                'descricao' => __('Infraestrutura de telecom'),
                'rota' => route('estacoes.index'),
                'icone' => 'signal',
                'contagem' => $stats['estacoes'],
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
