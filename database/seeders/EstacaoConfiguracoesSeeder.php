<?php

namespace Database\Seeders;

use App\Models\Estacao;
use App\Models\EstacaoDetentor;
use App\Models\EstacaoStatus;
use App\Models\EstacaoTecnologia;
use App\Models\EstacaoTipoConexao;
use App\Models\EstacaoTipoEv;
use App\Models\EstacaoTipoInfra;
use Illuminate\Database\Seeder;

class EstacaoConfiguracoesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Estacao::TECNOLOGIAS as $valor) {
            EstacaoTecnologia::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }

        foreach (Estacao::TIPOS_CONEXAO as $valor) {
            EstacaoTipoConexao::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }

        foreach (Estacao::DETENTORES as $valor) {
            EstacaoDetentor::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }

        foreach (Estacao::TIPOS_INFRA as $valor) {
            EstacaoTipoInfra::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }

        foreach (Estacao::TIPOS_EV as $valor) {
            EstacaoTipoEv::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }

        foreach (Estacao::STATUS as $valor) {
            EstacaoStatus::updateOrCreate(['nome' => $valor], ['ativo' => true]);
        }
    }
}
