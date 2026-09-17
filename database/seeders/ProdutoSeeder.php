<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            [
                'nome' => 'Roteador Wi-Fi 6 AX3000',
                'codigo' => 'PROD-001',
                'categoria' => 'Equipamento',
                'descricao' => 'Roteador dual band com suporte a Wi-Fi 6 e gerenciamento remoto.',
                'preco' => 349.90,
                'observacoes' => 'Entregue em comodato para clientes do plano 500MB+.',
                'ativo' => true,
            ],
            [
                'nome' => 'Modem ONU GPON',
                'codigo' => 'PROD-002',
                'categoria' => 'Equipamento',
                'descricao' => 'Terminal óptico para conexões FTTH com 1 porta GE e 2 POTS.',
                'preco' => 189.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Switch 24 Portas PoE',
                'codigo' => 'PROD-003',
                'categoria' => 'Equipamento',
                'descricao' => 'Switch gerenciável com 450W PoE para rede de acesso.',
                'preco' => 2499.99,
                'observacoes' => 'Usado nos armários concentradores dos bairros.',
                'ativo' => true,
            ],
            [
                'nome' => 'Caixa de Passagem Externa',
                'codigo' => 'PROD-004',
                'categoria' => 'Infraestrutura',
                'descricao' => 'Caixa de proteção para emendas ópticas em poste.',
                'preco' => 45.50,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Cabos de Fibra Óptica 100m',
                'codigo' => 'PROD-005',
                'categoria' => 'Cabeamento',
                'descricao' => 'Rolo de fibra óptica monomodo drop com 100 metros.',
                'preco' => 129.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Antena Exterior 5GHz',
                'codigo' => 'PROD-006',
                'categoria' => 'Equipamento',
                'descricao' => 'Antena direcional para enlaces ponto a ponto.',
                'preco' => 899.90,
                'observacoes' => 'Em estoque baixo.',
                'ativo' => true,
            ],
            [
                'nome' => 'Conversor de Mídia 10/100/1000',
                'codigo' => 'PROD-007',
                'categoria' => 'Equipamento',
                'descricao' => 'Conversor óptico-elétrico para ligações com clientes P2P.',
                'preco' => 210.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Rack de Parede 6U',
                'codigo' => 'PROD-008',
                'categoria' => 'Infraestrutura',
                'descricao' => 'Rack compacto para instalação em prédios e condomínios.',
                'preco' => 380.00,
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Adaptador de Energia 12V',
                'codigo' => 'PROD-009',
                'categoria' => 'Acessório',
                'descricao' => 'Fonte de alimentação para ONUs e roteadores.',
                'preco' => 39.90,
                'observacoes' => 'Modelo descontinuado — apenas reposição.',
                'ativo' => false,
            ],
            [
                'nome' => 'Pigtail SC/APC',
                'codigo' => 'PROD-010',
                'categoria' => 'Acessório',
                'descricao' => 'Pigtail monomodo conectorizado para fusão de drop.',
                'preco' => 15.00,
                'observacoes' => null,
                'ativo' => true,
            ],
        ];

        foreach ($produtos as $dados) {
            Produto::updateOrCreate(
                ['codigo' => $dados['codigo']],
                $dados,
            );
        }
    }
}
