<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });

        Schema::table('estacoes', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });

        Schema::rename('nokia_projetos', 'tim_projetos');
        Schema::rename('nokia_relatorios', 'tim_relatorios');
        Schema::rename('nokia_projeto_etapas', 'tim_projeto_etapas');
        Schema::rename('nokia_projeto_historicos', 'tim_projeto_historicos');
        Schema::rename('nokia_projeto_anexos', 'tim_projeto_anexos');

        Schema::table('tim_relatorios', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });

        Schema::table('tim_projeto_etapas', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });

        Schema::table('tim_projeto_historicos', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });

        Schema::table('tim_projeto_anexos', function (Blueprint $table) {
            $table->renameColumn('projeto_nokia_id', 'projeto_tim_id');
        });
    }

    public function down(): void
    {
        Schema::table('tim_projeto_anexos', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });

        Schema::table('tim_projeto_historicos', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });

        Schema::table('tim_projeto_etapas', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });

        Schema::table('tim_relatorios', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });

        Schema::rename('tim_projetos', 'nokia_projetos');
        Schema::rename('tim_relatorios', 'nokia_relatorios');
        Schema::rename('tim_projeto_etapas', 'nokia_projeto_etapas');
        Schema::rename('tim_projeto_historicos', 'nokia_projeto_historicos');
        Schema::rename('tim_projeto_anexos', 'nokia_projeto_anexos');

        Schema::table('estacoes', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });

        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->renameColumn('projeto_tim_id', 'projeto_nokia_id');
        });
    }
};