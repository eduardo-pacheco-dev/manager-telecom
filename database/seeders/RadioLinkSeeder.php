<?php

namespace Database\Seeders;

use App\Models\Estacao;
use App\Models\RadioLink;
use Illuminate\Database\Seeder;

class RadioLinkSeeder extends Seeder
{
    public function run(): void
    {
        $estacoes = Estacao::orderBy('id')->pluck('id');

        if ($estacoes->count() < 2) {
            return;
        }

        $parA = fn (int $offset) => $estacoes->get($offset) ?? $estacoes->first();
        $parB = fn (int $offset) => $estacoes->get($offset + 1) ?? $estacoes->last();

        $radioLinks = [
            [
                'codigo' => 'RL-0001',
                'nome' => 'Backhaul Centro',
                'estacao_a_id' => $parA(0),
                'estacao_b_id' => $parB(0),
                'frequencia' => 23.000,
                'capacidade' => '1 Gbps',
                'canal' => '1E1',
                'polarizacao' => 'Dupla',
                'fabricante' => 'ERICSSON',
                'modelo' => 'MINI-LINK 6363',
                'distancia' => 12.5,
                'status' => 'Ativo',
                'data_ativacao' => '2023-05-10',
                'observacao' => 'Link de backhaul entre as estações.',
            ],
            [
                'codigo' => 'RL-0002',
                'nome' => 'Backhaul Norte',
                'estacao_a_id' => $parA(1),
                'estacao_b_id' => $parB(1),
                'frequencia' => 18.000,
                'capacidade' => '2 Gbps',
                'canal' => '2E1',
                'polarizacao' => 'Horizontal',
                'fabricante' => 'CERAGON',
                'modelo' => 'IP-20C',
                'distancia' => 8.2,
                'status' => 'Ativo',
                'data_ativacao' => '2022-11-03',
                'observacao' => null,
            ],
            [
                'codigo' => 'RL-0003',
                'nome' => 'Backhaul Sul',
                'estacao_a_id' => $parA(2),
                'estacao_b_id' => $parB(2),
                'frequencia' => 26.000,
                'capacidade' => '10 Gbps',
                'canal' => '3E1',
                'polarizacao' => 'Vertical',
                'fabricante' => 'HUAWEI',
                'modelo' => 'RTN 905',
                'distancia' => 20.0,
                'status' => 'Em implantação',
                'data_ativacao' => null,
                'observacao' => 'Aguardando ativação do equipamento.',
            ],
        ];

        foreach ($radioLinks as $dados) {
            RadioLink::updateOrCreate(
                ['codigo' => $dados['codigo']],
                $dados,
            );
        }
    }
}
