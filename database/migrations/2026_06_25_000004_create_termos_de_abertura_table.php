<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termos_de_abertura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->unique()->constrained('projetos')->cascadeOnDelete();
            $table->text('objetivo')->nullable();
            $table->text('justificativa')->nullable();
            $table->text('restricoes')->nullable();
            $table->text('premissas')->nullable();
            $table->text('entregas_esperadas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termos_de_abertura');
    }
};
