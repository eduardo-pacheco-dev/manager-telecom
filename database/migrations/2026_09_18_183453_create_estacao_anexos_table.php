<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estacao_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estacao_id')->constrained('estacoes')->cascadeOnDelete();
            $table->string('nome');
            $table->string('arquivo');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->timestamps();

            $table->index('estacao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estacao_anexos');
    }
};
