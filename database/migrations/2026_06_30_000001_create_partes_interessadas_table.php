<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partes_interessadas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->cascadeOnDelete();
            $table->string('nome', 150);
            $table->string('papel', 120)->nullable();
            $table->string('organizacao', 150)->nullable();
            $table->string('poder', 10);
            $table->string('interesse', 10);
            $table->text('estrategia_engajamento')->nullable();
            $table->timestamps();

            $table->index(['projeto_id', 'poder', 'interesse']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes_interessadas');
    }
};
