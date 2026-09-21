<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->string('tipo_valor')->default('servico')->after('categoria');
            $table->decimal('preco_medio', 10, 2)->nullable()->after('preco');
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn(['tipo_valor', 'preco_medio']);
        });
    }
};
