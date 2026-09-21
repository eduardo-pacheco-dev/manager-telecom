<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nokia_projeto_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_nokia_id')->constrained('nokia_projetos')->cascadeOnDelete();
            $table->string('etapa');
            $table->string('status')->default('Pendente');
            $table->date('data_conclusao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nokia_projeto_etapas');
    }
};
