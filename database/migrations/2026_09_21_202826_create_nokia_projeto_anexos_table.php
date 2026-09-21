<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nokia_projeto_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_nokia_id')->constrained('nokia_projetos')->cascadeOnDelete();
            $table->string('categoria');
            $table->string('nome');
            $table->string('arquivo');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->timestamps();

            $table->index('projeto_nokia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nokia_projeto_anexos');
    }
};
