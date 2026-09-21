<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nokia_projetos', function (Blueprint $table) {
            $table->string('os_fam_panoramica')->nullable();
            $table->string('os_fam_desinstalacao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('nokia_projetos', function (Blueprint $table) {
            $table->dropColumn(['os_fam_panoramica', 'os_fam_desinstalacao']);
        });
    }
};
