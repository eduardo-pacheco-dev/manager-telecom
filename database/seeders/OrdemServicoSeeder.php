<?php

namespace Database\Seeders;

use App\Models\OrdemServico;
use App\Models\RadioLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdemServicoSeeder extends Seeder
{
    public function run(): void
    {
        $radioLinks = RadioLink::orderBy('id')->pluck('id');
        $responsavel = User::orderBy('id')->value('id');

        if ($radioLinks->isEmpty()) {
            return;
        }

        $ordens = [
            [
                'codigo' => 'OS-0001',
                'titulo' => 'Alinhamento de antena no link RL-0001',
                'tipo' => 'Manutenção',
                'escopo' => 'Enlace',
                'status' => 'Aberta',
                'prioridade' => 'Alta',
                'radio_link_id' => $radioLinks->get(0) ?? $radioLinks->first(),
                'solicitante' => 'NOC',
                'responsavel_id' => $responsavel,
                'descricao' => 'Sinal degradado no enlace. Necessário alinhamento da antena na estação A e verificação do nível de recepção na estação B.',
                'data_abertura' => '2026-09-10',
                'data_agendamento' => '2026-09-20',
                'data_conclusao' => null,
            ],
            [
                'codigo' => 'OS-0002',
                'titulo' => 'Instalação de equipamento no link RL-0002',
                'tipo' => 'Instalação',
                'escopo' => 'Enlace',
                'status' => 'Em andamento',
                'prioridade' => 'Média',
                'radio_link_id' => $radioLinks->get(1) ?? $radioLinks->first(),
                'solicitante' => 'Engenharia',
                'responsavel_id' => $responsavel,
                'descricao' => 'Instalar rádio CERAGON IP-20C e configurar capacidade de 2 Gbps.',
                'data_abertura' => '2026-09-12',
                'data_agendamento' => '2026-09-18',
                'data_conclusao' => null,
            ],
            [
                'codigo' => 'OS-0003',
                'titulo' => 'Ativação do link RL-0003',
                'tipo' => 'Ativação',
                'escopo' => 'Enlace',
                'status' => 'Concluída',
                'prioridade' => 'Urgente',
                'radio_link_id' => $radioLinks->get(2) ?? $radioLinks->first(),
                'solicitante' => 'NOC',
                'responsavel_id' => $responsavel,
                'descricao' => 'Ativação do enlace após instalação dos equipamentos HUAWEI RTN 905.',
                'data_abertura' => '2026-08-25',
                'data_agendamento' => '2026-08-27',
                'data_conclusao' => '2026-08-27',
            ],
        ];

        foreach ($ordens as $dados) {
            OrdemServico::updateOrCreate(
                ['codigo' => $dados['codigo']],
                $dados,
            );
        }
    }
}
