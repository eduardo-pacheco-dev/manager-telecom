<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('titulo');
            $table->string('tipo')->nullable();
            $table->string('status')->nullable();
            $table->string('prioridade')->nullable();
            $table->foreignId('radio_link_id')->nullable()->constrained('radio_links')->nullOnDelete();
            $table->foreignId('estacao_a_id')->nullable()->constrained('estacoes')->nullOnDelete();
            $table->foreignId('estacao_b_id')->nullable()->constrained('estacoes')->nullOnDelete();
            $table->string('solicitante')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('descricao')->nullable();
            $table->date('data_abertura')->nullable();
            $table->date('data_agendamento')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('tipo');
            $table->index('prioridade');
            $table->index('radio_link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
    }
};
