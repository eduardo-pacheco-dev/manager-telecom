<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo')->nullable()->unique();
            $table->string('categoria')->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('categoria');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
