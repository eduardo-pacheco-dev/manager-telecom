<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estacoes', function (Blueprint $table) {
            $table->id();
            $table->string('site_id')->unique();
            $table->string('tipo_elemento')->nullable();
            $table->string('tecnologia')->nullable();
            $table->string('tipo_conexao')->nullable();
            $table->string('endereco_id')->nullable();
            $table->string('classificacao')->nullable();
            $table->date('data_aquisicao')->nullable();
            $table->date('data_construcao')->nullable();
            $table->date('data_ativacao')->nullable();
            $table->date('data_desativacao')->nullable();
            $table->date('data_cancelamento')->nullable();
            $table->string('tipo_contrato_area')->nullable();
            $table->string('detentor_area')->nullable();
            $table->string('tipo_contrato_infra')->nullable();
            $table->string('detentor_infra')->nullable();
            $table->string('tipo_infra')->nullable();
            $table->string('tipo_ev')->nullable();
            $table->string('fornecedor_ev')->nullable();
            $table->text('observacao')->nullable();
            $table->text('justificativa')->nullable();
            $table->string('tipo_logradouro')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('municipio')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('regional')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('status')->nullable();
            $table->string('tipo_torre')->nullable();
            $table->decimal('aev_nominal', 10, 2)->nullable();
            $table->decimal('area_solo', 10, 2)->nullable();
            $table->decimal('altura_estrutura', 10, 2)->nullable();
            $table->string('station_id')->nullable();
            $table->string('ordem_complexa')->nullable();
            $table->text('observacao_thq')->nullable();
            $table->string('situacao')->nullable();
            $table->string('ots')->nullable();
            $table->timestamps();

            $table->index('tipo_elemento');
            $table->index('municipio');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estacoes');
    }
};
