<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('cpf')->unique();
            $table->string('telefone')->nullable();
            $table->string('cargo')->nullable();
            $table->string('departamento')->nullable();
            $table->date('data_admissao')->nullable();
            $table->decimal('salario', 10, 2)->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('departamento');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};
