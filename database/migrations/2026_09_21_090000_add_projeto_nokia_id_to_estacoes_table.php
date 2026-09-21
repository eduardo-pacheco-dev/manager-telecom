<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estacoes', function (Blueprint $table) {
            $table->foreignId('projeto_nokia_id')->nullable()->constrained('nokia_projetos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('estacoes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('projeto_nokia_id');
        });
    }
};
