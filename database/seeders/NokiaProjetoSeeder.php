<?php

namespace Database\Seeders;

use App\Models\NokiaProjeto;
use App\Models\OrdemServico;
use Illuminate\Database\Seeder;

class NokiaProjetoSeeder extends Seeder
{
    public function run(): void
    {
        $projetos = [
            [
                'codigo' => 'NOK-0001',
                'nome' => 'Implantação RAN TIM',
                'descricao' => 'Implantação de estações RAN para a operadora TIM em 2026.',
                'status' => 'Em andamento',
                'data_inicio' => '2026-01-10',
                'data_fim' => '2026-12-20',
                'ativo' => true,
            ],
            [
                'codigo' => 'NOK-0002',
                'nome' => 'Modernização 5G',
                'descricao' => 'Modernização de sites existentes para tecnologia 5G NR.',
                'status' => 'Em andamento',
                'data_inicio' => '2026-03-01',
                'data_fim' => '2026-11-30',
                'ativo' => true,
            ],
            [
                'codigo' => 'NOK-0003',
                'nome' => 'Expansão de cobertura',
                'descricao' => 'Novos sites para ampliação da cobertura no interior.',
                'status' => 'Planejamento',
                'data_inicio' => '2026-08-15',
                'data_fim' => null,
                'ativo' => true,
            ],
            [
                'codigo' => 'NOK-0004',
                'nome' => 'Ativação de novos sites',
                'descricao' => 'Ativação comercial dos sites implantados no primeiro semestre.',
                'status' => 'Concluído',
                'data_inicio' => '2026-01-05',
                'data_fim' => '2026-06-30',
                'ativo' => true,
            ],
            [
                'codigo' => 'NOK-0005',
                'nome' => 'Troca de equipamentos',
                'descricao' => 'Substituição de rádios legados por equipamentos Nokia.',
                'status' => 'Pausado',
                'data_inicio' => '2026-04-01',
                'data_fim' => null,
                'ativo' => false,
            ],
        ];

        $ordens = OrdemServico::orderBy('id')->pluck('id');

        foreach ($projetos as $indice => $dados) {
            $projeto = NokiaProjeto::updateOrCreate(
                ['codigo' => $dados['codigo']],
                $dados,
            );

            $projeto->ensureEtapas();

            $vinculadas = $ordens->slice($indice * 3, 3);

            if ($vinculadas->isNotEmpty()) {
                OrdemServico::whereIn('id', $vinculadas)
                    ->update(['projeto_nokia_id' => $projeto->id]);
            }
        }
    }
}
