<?php

namespace App\Modules\Projetos\Support;

use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\Turma;

class GeradorDeCodigoDoProjeto
{
    public function gerar(Turma $turma): string
    {
        $prefixo = "{$turma->codigo}-P";
        $ultimoCodigo = Projeto::query()
            ->where('turma_id', $turma->id)
            ->where('codigo', 'like', "{$prefixo}%")
            ->orderByDesc('codigo')
            ->value('codigo');

        $sequencia = $this->proximaSequencia($ultimoCodigo);

        return sprintf('%s%03d', $prefixo, $sequencia);
    }

    private function proximaSequencia(?string $ultimoCodigo): int
    {
        if ($ultimoCodigo === null) {
            return 1;
        }

        if (preg_match('/-P(\d+)$/', $ultimoCodigo, $matches) !== 1) {
            return 1;
        }

        return ((int) $matches[1]) + 1;
    }
}
