<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_link_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radio_link_id')->constrained('radio_links')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('conteudo');
            $table->timestamps();
            $table->index('radio_link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_link_comentarios');
    }
};
