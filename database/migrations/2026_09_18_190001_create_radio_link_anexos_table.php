<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radio_link_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radio_link_id')->constrained('radio_links')->cascadeOnDelete();
            $table->string('nome');
            $table->string('arquivo');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->timestamps();
            $table->index('radio_link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radio_link_anexos');
    }
};
