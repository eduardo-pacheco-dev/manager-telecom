<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nokia_projeto_etapas', function (Blueprint $table) {
            $table->date('data_planejada')->nullable()->after('data_baseline');
            $table->date('data_real')->nullable()->after('data_planejada');
        });
    }

    public function down(): void
    {
        Schema::table('nokia_projeto_etapas', function (Blueprint $table) {
            $table->dropColumn(['data_planejada', 'data_real']);
        });
    }
};
