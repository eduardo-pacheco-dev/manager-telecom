<?php

namespace Database\Seeders;

use App\Models\Servico;
use Illuminate\Database\Seeder;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        $servicos = [
            [
                'nome' => 'Instalação de Fibra Óptica',
                'codigo' => 'SRV-001',
                'categoria' => 'Instalação',
                'descricao' => 'Instalação completa do drop óptico, ONU e configuração do roteador residencial.',
                'preco' => 149.90,
                'observacoes' => 'Isento de cobrança na assinatura do plano 500MB+.',
                'ativo' => true,
            ],
            [
                'nome' => 'Manutenção Corretiva',
                'codigo' => 'SRV-002',
                'categoria' => 'Manutenção',
                'descricao' => 'Diagnóstico e reparo de falhas na rede local do cliente.',
                'preco' => 120.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Ativação de Internet 500MB',
                'codigo' => 'SRV-003',
                'categoria' => 'Configuração',
                'descricao' => 'Ativação do plano de internet com teste de velocidade e sinal óptico.',
                'preco' => 89.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Suporte Técnico Remoto',
                'codigo' => 'SRV-004',
                'categoria' => 'Suporte',
                'descricao' => 'Atendimento remoto para configuração de roteadores e redes Wi-Fi.',
                'preco' => 45.00,
                'observacoes' => 'Inclui até 30 minutos de atendimento.',
                'ativo' => true,
            ],
            [
                'nome' => 'Cabeamento Estruturado',
                'codigo' => 'SRV-005',
                'categoria' => 'Instalação',
                'descricao' => 'Projeto e execução de cabeamento estruturado para empresas.',
                'preco' => 350.00,
                'observacoes' => 'Valor por ponto instalado.',
                'ativo' => true,
            ],
            [
                'nome' => 'Configuração de Roteador',
                'codigo' => 'SRV-006',
                'categoria' => 'Configuração',
                'descricao' => 'Configuração avançada de roteadores, QoS e redes de convidados.',
                'preco' => 65.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Telefonia VoIP',
                'codigo' => 'SRV-007',
                'categoria' => 'Configuração',
                'descricao' => 'Configuração de linha VoIP e aparelhos para empresas.',
                'preco' => 150.00,
                'observacoes' => 'Mensalidade de 3 linhas inclusa.',
                'ativo' => true,
            ],
            [
                'nome' => 'Locação de Equipamento',
                'codigo' => 'SRV-008',
                'categoria' => 'Suporte',
                'descricao' => 'Locação mensal de roteador ou ONU para clientes.',
                'preco' => 19.90,
                'observacoes' => 'Cobrado mensalmente em fatura.',
                'ativo' => true,
            ],
            [
                'nome' => 'Reativação de Conta',
                'codigo' => 'SRV-009',
                'categoria' => 'Manutenção',
                'descricao' => 'Reativação de serviço suspenso por inadimplência.',
                'preco' => 50.00,
                'observacoes' => 'Exige pagamento do débito pendente.',
                'ativo' => false,
            ],
            [
                'nome' => 'Mudança de Endereço',
                'codigo' => 'SRV-010',
                'categoria' => 'Instalação',
                'descricao' => 'Transferência do serviço para novo endereço residencial.',
                'preco' => 99.90,
                'observacoes' => null,
                'ativo' => true,
            ],
        ];

        foreach ($servicos as $dados) {
            Servico::updateOrCreate(
                ['codigo' => $dados['codigo']],
                $dados,
            );
        }
    }
}
