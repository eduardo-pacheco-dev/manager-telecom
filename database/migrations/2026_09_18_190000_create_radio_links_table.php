<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_links', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nome')->nullable();
            $table->foreignId('estacao_a_id')->constrained('estacoes')->cascadeOnDelete();
            $table->foreignId('estacao_b_id')->constrained('estacoes')->cascadeOnDelete();
            $table->decimal('frequencia', 10, 3)->nullable();
            $table->string('capacidade')->nullable();
            $table->string('canal')->nullable();
            $table->string('polarizacao')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('modelo')->nullable();
            $table->decimal('distancia', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->date('data_ativacao')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index('estacao_a_id');
            $table->index('estacao_b_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_links');
    }
};
