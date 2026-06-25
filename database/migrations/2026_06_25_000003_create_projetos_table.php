<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('codigo')->unique();
            $table->text('descricao')->nullable();
            $table->string('situacao', 40)->default('em_iniciacao');
            $table->timestamps();

            $table->index(['turma_id', 'situacao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
