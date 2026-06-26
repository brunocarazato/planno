<?php

namespace App\Modules\Turmas\Support;

use App\Modules\Turmas\Models\Turma;

class GeradorDeCodigoDaTurma
{
    public function gerar(?string $periodo): string
    {
        [$ano, $semestre] = $this->referencia($periodo);
        $prefixo = "TUR-{$ano}-{$semestre}";
        $ultimoCodigo = Turma::query()
            ->where('codigo', 'like', "{$prefixo}-%")
            ->orderByDesc('codigo')
            ->value('codigo');

        $sequencia = $this->proximaSequencia($ultimoCodigo);

        return sprintf('%s-%03d', $prefixo, $sequencia);
    }

    /**
     * @return array{int, int}
     */
    private function referencia(?string $periodo): array
    {
        if ($periodo !== null) {
            if (preg_match('/(20\d{2})\D*([12])\b/', $periodo, $matches) === 1) {
                return [(int) $matches[1], (int) $matches[2]];
            }

            if (preg_match('/([12])\D*(?:semestre|sem)\D*(20\d{2})/i', $periodo, $matches) === 1) {
                return [(int) $matches[2], (int) $matches[1]];
            }
        }

        $agora = now();

        return [(int) $agora->year, $agora->month <= 6 ? 1 : 2];
    }

    private function proximaSequencia(?string $ultimoCodigo): int
    {
        if ($ultimoCodigo === null) {
            return 1;
        }

        if (preg_match('/-(\d+)$/', $ultimoCodigo, $matches) !== 1) {
            return 1;
        }

        return ((int) $matches[1]) + 1;
    }
}
