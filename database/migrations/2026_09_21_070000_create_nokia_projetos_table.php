<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nokia_projetos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->string('status')->default('Em andamento');
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->foreignId('projeto_nokia_id')->nullable()->constrained('nokia_projetos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->dropConstrainedForeignId('projeto_nokia_id');
        });

        Schema::dropIfExists('nokia_projetos');
    }
};
