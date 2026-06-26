<?php

namespace App\Modules\Turmas\Support;

use App\Modules\Turmas\Models\Turma;

class GeradorDeCodigoDaTurma
{
    public function gerar(int $ano, string $periodo): string
    {
        $prefixo = "TUR-{$ano}-{$periodo}";
        $ultimoCodigo = Turma::query()
            ->where('codigo', 'like', "{$prefixo}-%")
            ->orderByDesc('codigo')
            ->value('codigo');

        $sequencia = $this->proximaSequencia($ultimoCodigo);

        return sprintf('%s-%03d', $prefixo, $sequencia);
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
