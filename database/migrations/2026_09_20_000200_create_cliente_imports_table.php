<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('arquivo');
            $table->string('nome_original');
            $table->string('status')->default('pendente');
            $table->unsignedBigInteger('total_linhas')->nullable();
            $table->unsignedBigInteger('processadas')->default(0);
            $table->unsignedBigInteger('importadas')->default(0);
            $table->unsignedBigInteger('ignoradas')->default(0);
            $table->text('erro')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_imports');
    }
};
