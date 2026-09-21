<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nokia_relatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_nokia_id')->constrained('nokia_projetos')->cascadeOnDelete();
            $table->foreignId('ordem_servico_id')->nullable()->constrained('ordens_servico')->nullOnDelete();
            $table->foreignId('estacao_id')->nullable()->constrained('estacoes')->nullOnDelete();
            $table->date('data_inicio')->nullable();
            $table->date('data_planejada')->nullable();
            $table->date('data_real')->nullable();
            $table->string('status')->default('Pendente');
            $table->string('observacao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nokia_relatorios');
    }
};
