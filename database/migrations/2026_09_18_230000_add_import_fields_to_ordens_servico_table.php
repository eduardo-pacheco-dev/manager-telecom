<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->string('projeto')->nullable();
            $table->string('end_id_a')->nullable();
            $table->string('end_id_b')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('coordenador')->nullable();
            $table->string('oc_tim')->nullable();
            $table->string('chave_mw')->nullable();
            $table->string('smp_nokia')->nullable();
            $table->text('observacao')->nullable();
            $table->json('dados_brutos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->dropColumn([
                'projeto', 'end_id_a', 'end_id_b', 'supervisor', 'coordenador',
                'oc_tim', 'chave_mw', 'smp_nokia', 'observacao', 'dados_brutos',
            ]);
        });
    }
};
