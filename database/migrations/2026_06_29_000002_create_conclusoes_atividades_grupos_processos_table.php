<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conclusoes_atividades_grupos_processos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trilha_id')->constrained('trilhas_grupos_processos')->cascadeOnDelete();
            $table->string('chave_atividade', 100);
            $table->foreignId('concluida_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('concluida_em');
            $table->timestamps();

            $table->unique(['trilha_id', 'chave_atividade'], 'conclusao_trilha_atividade_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conclusoes_atividades_grupos_processos');
    }
};
