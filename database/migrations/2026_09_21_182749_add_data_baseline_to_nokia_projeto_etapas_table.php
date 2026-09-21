<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nokia_projeto_etapas', function (Blueprint $table) {
            $table->date('data_baseline')->nullable()->after('data_conclusao');
        });
    }

    public function down(): void
    {
        Schema::table('nokia_projeto_etapas', function (Blueprint $table) {
            $table->dropColumn('data_baseline');
        });
    }
};
