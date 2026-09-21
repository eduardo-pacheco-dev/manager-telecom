<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estacoes', function (Blueprint $table) {
            $table->string('operadora')->nullable()->after('tipo_conexao');
        });

        Schema::create('estacao_operadoras', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estacao_operadoras');

        Schema::table('estacoes', function (Blueprint $table) {
            $table->dropColumn('operadora');
        });
    }
};
