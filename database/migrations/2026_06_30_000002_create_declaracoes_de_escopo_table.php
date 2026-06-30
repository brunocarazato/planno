<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaracoes_de_escopo', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projeto_id')->unique()->constrained('projetos')->cascadeOnDelete();
            $table->text('descricao')->nullable();
            $table->text('inclui')->nullable();
            $table->text('exclusoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaracoes_de_escopo');
    }
};
