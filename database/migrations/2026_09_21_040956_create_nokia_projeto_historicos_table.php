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
        Schema::create('nokia_projeto_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_nokia_id')->constrained('nokia_projetos')->cascadeOnDelete();
            $table->string('tipo', 50);
            $table->string('descricao');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nokia_projeto_historicos');
    }
};
