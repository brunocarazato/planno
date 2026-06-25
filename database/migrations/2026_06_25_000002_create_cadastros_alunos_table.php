<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cadastros_alunos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('ra', 40);
            $table->string('status', 30)->default('pendente');
            $table->text('motivo_reprovacao')->nullable();
            $table->timestamp('avaliado_em')->nullable();
            $table->date('valido_ate')->nullable();
            $table->timestamps();

            $table->index(['turma_id', 'status']);
            $table->index(['ra', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cadastros_alunos');
    }
};
