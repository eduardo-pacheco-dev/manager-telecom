<?php

namespace Database\Seeders;

use App\Models\Colaborador;
use Illuminate\Database\Seeder;

class ColaboradorSeeder extends Seeder
{
    public function run(): void
    {
        $colaboradores = [
            [
                'nome' => 'Carlos Eduardo Almeida',
                'email' => 'carlos.almeida@gmail.com',
                'telefone' => '(11) 99876-5432',
                'cargo' => 'Técnico de Campo',
                'departamento' => 'Operações',
                'categoria' => 'CLT',
                'data_admissao' => '2019-03-11',
                'salario' => 4650.00,
                'endereco' => 'Rua Augusta, 1234',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01310-100',
                'observacoes' => 'Especialista em redes FTTH e atendimento residencial.',
                'ativo' => true,
            ],
            [
                'nome' => 'Fernanda Souza Lima',
                'email' => 'fernanda.lima@gmail.com',
                'telefone' => '(11) 97654-3210',
                'cargo' => 'Engenheira de Redes',
                'departamento' => 'Operações',
                'categoria' => 'CLT',
                'data_admissao' => '2018-07-02',
                'salario' => 11800.00,
                'endereco' => 'Av. Paulista, 1578',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01310-200',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Roberto Ferreira Nunes',
                'email' => 'roberto.nunes@gmail.com',
                'telefone' => '(11) 3456-7890',
                'cargo' => 'Analista de NOC',
                'departamento' => 'Manutenção',
                'categoria' => 'PJ',
                'data_admissao' => '2021-01-18',
                'salario' => 9200.00,
                'endereco' => 'Rua dos Pinheiros, 500',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '05422-000',
                'observacoes' => 'Plantão de monitoramento 24x7.',
                'ativo' => true,
            ],
            [
                'nome' => 'Juliana Martins Rocha',
                'email' => 'juliana.rocha@gmail.com',
                'telefone' => '(19) 99887-6655',
                'cargo' => 'Coordenadora Comercial',
                'departamento' => 'Comercial',
                'categoria' => 'CLT',
                'data_admissao' => '2020-09-14',
                'salario' => 7900.00,
                'endereco' => 'Av. Brasil, 250',
                'cidade' => 'Campinas',
                'estado' => 'SP',
                'cep' => '13070-070',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Marcos Vinícius Pereira',
                'email' => 'marcos.pereira@gmail.com',
                'telefone' => '(19) 3345-6789',
                'cargo' => 'Consultor de Vendas',
                'departamento' => 'Comercial',
                'categoria' => 'PJ',
                'data_admissao' => '2022-05-23',
                'salario' => 5400.00,
                'endereco' => 'Rua Barão de Jaguara, 890',
                'cidade' => 'Campinas',
                'estado' => 'SP',
                'cep' => '13015-003',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Patrícia Gomes Barbosa',
                'email' => 'patricia.barbosa@gmail.com',
                'telefone' => '(21) 98123-4567',
                'cargo' => 'Analista Financeira',
                'departamento' => 'Financeiro',
                'categoria' => 'CLT',
                'data_admissao' => '2017-11-06',
                'salario' => 6900.00,
                'endereco' => 'Rua do Ouvidor, 60',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'cep' => '20040-030',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Diego Santos Carvalho',
                'email' => 'diego.carvalho@gmail.com',
                'telefone' => '(21) 98765-1234',
                'cargo' => 'Técnico de Fibra Óptica',
                'departamento' => 'Manutenção',
                'categoria' => 'CLT',
                'data_admissao' => '2023-02-20',
                'salario' => 4100.00,
                'endereco' => 'Rua Voluntários da Pátria, 320',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'cep' => '22270-000',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Amanda Ribeiro Teixeira',
                'email' => 'amanda.teixeira@gmail.com',
                'telefone' => '(31) 99654-3210',
                'cargo' => 'Desenvolvedora de Sistemas',
                'departamento' => 'TI',
                'categoria' => 'Freelancer',
                'data_admissao' => '2024-04-10',
                'salario' => 8200.00,
                'endereco' => 'Av. Afonso Pena, 2100',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cep' => '30130-000',
                'observacoes' => 'Atua em projetos pontuais de integração.',
                'ativo' => true,
            ],
            [
                'nome' => 'Gustavo Henrique Costa',
                'email' => 'gustavo.costa@gmail.com',
                'telefone' => '(41) 3112-8899',
                'cargo' => 'Supervisor de Operações',
                'departamento' => 'Operações',
                'categoria' => 'CLT',
                'data_admissao' => '2016-08-29',
                'salario' => 9800.00,
                'endereco' => 'Rua XV de Novembro, 1200',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'cep' => '80020-310',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Larissa Campos Moraes',
                'email' => 'larissa.moraes@gmail.com',
                'telefone' => '(51) 99812-3456',
                'cargo' => 'Analista de Suporte',
                'departamento' => 'Manutenção',
                'categoria' => 'PJ',
                'data_admissao' => '2023-08-07',
                'salario' => 6200.00,
                'endereco' => 'Rua dos Andradas, 850',
                'cidade' => 'Porto Alegre',
                'estado' => 'RS',
                'cep' => '90020-004',
                'observacoes' => null,
                'ativo' => false,
            ],
            [
                'nome' => 'Thiago Alves Pinto',
                'email' => 'thiago.pinto@gmail.com',
                'telefone' => '(71) 98745-6321',
                'cargo' => 'Instalador Residencial',
                'departamento' => 'Operações',
                'categoria' => 'Freelancer',
                'data_admissao' => '2024-11-18',
                'salario' => 3800.00,
                'endereco' => 'Av. Tancredo Neves, 620',
                'cidade' => 'Salvador',
                'estado' => 'BA',
                'cep' => '41820-000',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Camila Oliveira Duarte',
                'email' => 'camila.duarte@gmail.com',
                'telefone' => '(61) 3346-7822',
                'cargo' => 'Assistente Administrativa',
                'departamento' => 'Administrativo',
                'categoria' => 'CLT',
                'data_admissao' => '2021-06-30',
                'salario' => 3500.00,
                'endereco' => 'Setor Comercial Sul, Qd 01, Bl A, 100',
                'cidade' => 'Brasília',
                'estado' => 'DF',
                'cep' => '70390-000',
                'observacoes' => null,
                'ativo' => true,
            ],
            [
                'nome' => 'Rafael Nogueira Santana',
                'email' => 'rafael.santana@gmail.com',
                'telefone' => '(62) 98456-3210',
                'cargo' => 'Controlador de Qualidade',
                'departamento' => 'Administrativo',
                'categoria' => 'PJ',
                'data_admissao' => '2023-03-15',
                'salario' => 5500.00,
                'endereco' => 'Av. Goiás, 745',
                'cidade' => 'Goiânia',
                'estado' => 'GO',
                'cep' => '74040-010',
                'observacoes' => 'Afastado para treinamento de certificação.',
                'ativo' => false,
            ],
            [
                'nome' => 'Beatriz Figueiredo Ramos',
                'email' => 'beatriz.ramos@gmail.com',
                'telefone' => '(81) 98877-1122',
                'cargo' => 'Analista de Cobrança',
                'departamento' => 'Financeiro',
                'categoria' => 'Freelancer',
                'data_admissao' => '2025-01-09',
                'salario' => 4800.00,
                'endereco' => 'Av. Conde da Boa Vista, 410',
                'cidade' => 'Recife',
                'estado' => 'PE',
                'cep' => '50060-000',
                'observacoes' => null,
                'ativo' => false,
            ],
        ];

        foreach ($colaboradores as $index => $dados) {
            Colaborador::updateOrCreate(
                ['email' => $dados['email']],
                ['cpf' => $this->validCpf(100000000 + $index), ...$dados],
            );
        }
    }

    private function validCpf(int $base): string
    {
        $digits = str_pad((string) $base, 9, '0', STR_PAD_LEFT);
        $firstDigit = $this->checkDigit($digits);
        $secondDigit = $this->checkDigit($digits.$firstDigit, 10);

        return vsprintf('%s.%s.%s-%s%s', [
            substr($digits, 0, 3),
            substr($digits, 3, 3),
            substr($digits, 6, 3),
            $firstDigit,
            $secondDigit,
        ]);
    }

    private function checkDigit(string $digits, int $length = 9): int
    {
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $digits[$i] * ($length + 1 - $i);
        }

        $rest = $sum % 11;

        return $rest < 2 ? 0 : 11 - $rest;
    }
}
