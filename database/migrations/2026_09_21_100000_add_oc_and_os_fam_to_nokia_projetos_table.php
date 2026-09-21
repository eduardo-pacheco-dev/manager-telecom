<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nokia_projetos', function (Blueprint $table) {
            $table->string('oc')->nullable();
            $table->string('os_fam_entrega')->nullable();
            $table->string('os_fam_instalacao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nokia_projetos', function (Blueprint $table) {
            $table->dropColumn(['oc', 'os_fam_entrega', 'os_fam_instalacao']);
        });
    }
};
