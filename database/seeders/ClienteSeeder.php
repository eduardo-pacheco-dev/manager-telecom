<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nome' => 'TechNova Telecomunicações Ltda',
                'email' => 'contato@technova.com.br',
                'telefone' => '(11) 4002-8922',
                'segmento' => 'Corporativo',
                'endereco' => 'Av. das Nações Unidas, 12000',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '04578-910',
                'observacoes' => 'Cliente corporativo com contrato de link dedicado.',
                'ativo' => true,
            ],
            [
                'nome' => 'Condomínio Residencial Jardim das Flores',
                'email' => 'sindico@jardimflores.com.br',
                'telefone' => '(19) 3222-1144',
                'segmento' => 'Residencial',
                'endereco' => 'Rua das Flores, 100',
                'cidade' => 'Campinas',
                'estado' => 'SP',
                'cep' => '13080-100',
                'observacoes' => 'Condomínio com fibra até o apartamento.',
                'ativo' => true,
            ],
            [
                'nome' => 'Supermercados Economia S.A.',
                'email' => 'ti@economia.com.br',
                'telefone' => '(31) 3345-7788',
                'segmento' => 'Varejo',
                'endereco' => 'Av. do Contorno, 3000',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cep' => '30110-012',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Construtora Horizonte Ltda',
                'email' => 'obras@horizonte.com.br',
                'telefone' => '(41) 3013-5566',
                'segmento' => 'Construtora',
                'endereco' => 'Rua Comendador Araújo, 555',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'cep' => '80420-000',
                'observacoes' => 'Instalação de fibra em canteiro de obras.',
                'ativo' => true,
            ],
            [
                'nome' => 'Prefeitura Municipal de Nova Cidade',
                'email' => 'licitacao@novacidade.gov.br',
                'telefone' => '(62) 3221-8899',
                'segmento' => 'Governo',
                'endereco' => 'Praça da Matriz, 1',
                'cidade' => 'Nova Cidade',
                'estado' => 'GO',
                'cep' => '74000-000',
                'observacoes' => 'Contrato de interligação entre repartições públicas.',
                'ativo' => true,
            ],
            [
                'nome' => 'Distribuidora Centro-Oeste Atacado',
                'email' => 'compras@centrooeste.com.br',
                'telefone' => '(85) 3232-4455',
                'segmento' => 'Atacado',
                'endereco' => 'Av. Washington Soares, 1500',
                'cidade' => 'Fortaleza',
                'estado' => 'CE',
                'cep' => '60811-905',
                'observacoes' => null,
                'ativo' => false,
            ],
            [
                'nome' => 'Clínica Vida Plena',
                'email' => 'recepcao@vidaplena.med.br',
                'telefone' => '(51) 3322-7788',
                'segmento' => 'Corporativo',
                'endereco' => 'Rua Padre Chagas, 290',
                'cidade' => 'Porto Alegre',
                'estado' => 'RS',
                'cep' => '90570-080',
                'observacoes' => 'Plano de dados dedicado para prontuário eletrônico.',
                'ativo' => true,
            ],
            [
                'nome' => 'Farmácia Popular da Cidade',
                'email' => 'loja@farmaciadacidade.com.br',
                'telefone' => '(71) 3321-9900',
                'segmento' => 'Varejo',
                'endereco' => 'Av. Sete de Setembro, 2000',
                'cidade' => 'Salvador',
                'estado' => 'BA',
                'cep' => '40080-002',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Hotel Ouro Preto',
                'email' => 'reservas@hotelouropreto.com.br',
                'telefone' => '(31) 3551-2233',
                'segmento' => 'Corporativo',
                'endereco' => 'Rua Direita, 95',
                'cidade' => 'Ouro Preto',
                'estado' => 'MG',
                'cep' => '35400-000',
                'observacoes' => 'Wi-Fi para hóspedes com fibra óptica.',
                'ativo' => true,
            ],
            [
                'nome' => 'Escola Conecta Educação',
                'email' => 'contato@conecta.edu.br',
                'telefone' => '(27) 3225-4411',
                'segmento' => 'Corporativo',
                'endereco' => 'Av. Nossa Senhora da Penha, 1200',
                'cidade' => 'Vitória',
                'estado' => 'ES',
                'cep' => '29045-402',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Padaria Pão Dourado',
                'email' => 'atendimento@paodourado.com.br',
                'telefone' => '(81) 3224-5566',
                'segmento' => 'Residencial',
                'endereco' => 'Rua da Aurora, 340',
                'cidade' => 'Recife',
                'estado' => 'PE',
                'cep' => '50050-000',
                'observacoes' => 'Ponto comercial pequeno com internet básica.',
                'ativo' => true,
            ],
            [
                'nome' => 'Indústria Metalúrgica Forte Aço',
                'email' => 'comercial@forteaco.com.br',
                'telefone' => '(47) 3324-7788',
                'segmento' => 'Corporativo',
                'endereco' => 'Rodovia BR-101, Km 25',
                'cidade' => 'Joinville',
                'estado' => 'SC',
                'cep' => '89219-900',
                'observacoes' => 'Link dedicado para controle industrial.',
                'ativo' => false,
            ],
        ];

        foreach ($clientes as $index => $dados) {
            Cliente::updateOrCreate(
                ['email' => $dados['email']],
                ['documento' => $this->validCnpj(10000000000000 + $index), ...$dados],
            );
        }
    }

    private function validCnpj(int $base): string
    {
        $digits = substr(str_pad((string) $base, 12, '0', STR_PAD_LEFT), 0, 12);
        $digits .= $this->checkDigit($digits);
        $digits .= $this->checkDigit($digits, 13);

        return vsprintf('%s.%s.%s/%s-%s', [
            substr($digits, 0, 2),
            substr($digits, 2, 3),
            substr($digits, 5, 3),
            substr($digits, 8, 4),
            substr($digits, 12, 2),
        ]);
    }

    private function checkDigit(string $digits, int $length = 12): int
    {
        $weights = $length === 12
            ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
            : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }

        $rest = $sum % 11;

        return $rest < 2 ? 0 : 11 - $rest;
    }
}
