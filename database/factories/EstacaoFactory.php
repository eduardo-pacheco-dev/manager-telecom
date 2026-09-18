<?php

namespace Database\Factories;

use App\Models\Estacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estacao>
 */
class EstacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_id' => fake()->unique()->bothify('??####'),
            'tipo_elemento' => fake()->randomElement(Estacao::TIPOS_ELEMENTO),
            'tecnologia' => fake()->randomElement(Estacao::TECNOLOGIAS),
            'tipo_conexao' => fake()->randomElement(Estacao::TIPOS_CONEXAO),
            'endereco_id' => fake()->unique()->bothify('??##_####'),
            'classificacao' => fake()->randomElement(Estacao::CLASSIFICACOES),
            'data_aquisicao' => fake()->dateTimeBetween('-10 years', 'now'),
            'data_construcao' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'data_ativacao' => fake()->optional()->dateTimeBetween('-10 years', 'now'),
            'data_desativacao' => null,
            'data_cancelamento' => null,
            'tipo_contrato_area' => fake()->optional()->randomElement(Estacao::TIPOS_CONTRATO),
            'detentor_area' => fake()->randomElement(Estacao::DETENTORES),
            'tipo_contrato_infra' => fake()->randomElement(Estacao::TIPOS_CONTRATO),
            'detentor_infra' => fake()->randomElement(Estacao::DETENTORES),
            'tipo_infra' => fake()->randomElement(Estacao::TIPOS_INFRA),
            'tipo_ev' => fake()->optional()->randomElement(Estacao::TIPOS_EV),
            'fornecedor_ev' => fake()->optional()->randomElement(Estacao::FORNECEDORES_EV),
            'observacao' => fake()->optional(0.3)->sentence(),
            'justificativa' => fake()->optional(0.2)->sentence(),
            'tipo_logradouro' => fake()->randomElement(Estacao::TIPOS_LOGRADOURO),
            'logradouro' => fake()->streetName(),
            'numero' => fake()->numerify('###'),
            'complemento' => fake()->optional(0.4)->words(3, true),
            'bairro' => fake()->word(),
            'municipio' => fake()->city(),
            'estado' => fake()->randomElement(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']),
            'cep' => fake()->numerify('#####-###'),
            'regional' => fake()->randomElement(Estacao::REGIONAIS),
            'latitude' => fake()->latitude(-30, 5),
            'longitude' => fake()->longitude(-75, -30),
            'status' => fake()->randomElement(Estacao::STATUS),
            'tipo_torre' => fake()->optional()->randomElement(['METALICA TRIANGULAR', 'METALICA QUADRANGULAR', 'ESTAIADA', 'POSTE']),
            'aev_nominal' => fake()->optional()->randomFloat(2, 0, 100),
            'area_solo' => fake()->optional()->randomFloat(2, 0, 1000),
            'altura_estrutura' => fake()->optional()->randomFloat(2, 20, 120),
            'station_id' => fake()->optional()->bothify('#######'),
            'ordem_complexa' => fake()->optional()->numerify('###'),
            'observacao_thq' => fake()->optional(0.2)->sentence(),
            'situacao' => fake()->optional()->randomElement(Estacao::SITUACOES),
            'ots' => fake()->optional(0.2)->randomElement(['Sim', 'Não']),
        ];
    }

    public function aquisitada(): static
    {
        return $this->state(fn () => ['status' => 'Aquisitado']);
    }

    public function emOperacao(): static
    {
        return $this->state(fn () => ['situacao' => 'Em operação']);
    }
}
