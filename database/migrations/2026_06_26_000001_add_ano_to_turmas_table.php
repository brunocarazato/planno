<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->unsignedSmallInteger('ano')->nullable();
        });

        DB::table('turmas')
            ->select(['id', 'periodo'])
            ->orderBy('id')
            ->chunkById(100, function ($turmas): void {
                foreach ($turmas as $turma) {
                    [$periodo, $ano] = $this->extrairPeriodoEAno($turma->periodo);

                    if ($periodo === null || $ano === null) {
                        continue;
                    }

                    DB::table('turmas')
                        ->where('id', $turma->id)
                        ->update([
                            'periodo' => $periodo,
                            'ano' => $ano,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn('ano');
        });
    }

    /**
     * @return array{?string, ?int}
     */
    private function extrairPeriodoEAno(?string $periodo): array
    {
        if ($periodo === null) {
            return [null, null];
        }

        if (preg_match('/(20\d{2})\D*([12])\b/', $periodo, $matches) === 1) {
            return [$matches[2], (int) $matches[1]];
        }

        if (preg_match('/([12])\D*(?:semestre|sem)\D*(20\d{2})/i', $periodo, $matches) === 1) {
            return [$matches[1], (int) $matches[2]];
        }

        return [null, null];
    }
};
