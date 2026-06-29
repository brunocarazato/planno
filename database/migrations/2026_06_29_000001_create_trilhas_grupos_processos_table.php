<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trilhas_grupos_processos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projeto_id')->unique()->constrained('projetos')->cascadeOnDelete();
            $table->timestamps();
        });

        $agora = now();
        $trilhas = DB::table('projetos')
            ->pluck('id')
            ->map(fn (int $projetoId) => [
                'projeto_id' => $projetoId,
                'created_at' => $agora,
                'updated_at' => $agora,
            ])
            ->all();

        if ($trilhas !== []) {
            DB::table('trilhas_grupos_processos')->insert($trilhas);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trilhas_grupos_processos');
    }
};
