<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->string('codigo_personalizado')->nullable()->after('codigo');
            $table->string('codigo_cliente')->nullable()->after('codigo_personalizado');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('ordem_complexa')->nullable()->after('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::table('ordens_servico', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cliente_id');
            $table->dropColumn(['codigo_personalizado', 'codigo_cliente', 'ordem_complexa']);
        });
    }
};
