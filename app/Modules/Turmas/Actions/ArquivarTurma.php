<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;
use Illuminate\Support\Carbon;

class ArquivarTurma
{
    public function executar(Turma $turma): Turma
    {
        $turma->update([
            'aceita_novos_cadastros' => false,
            'arquivada_em' => $turma->arquivada_em ?? Carbon::now(),
        ]);

        return $turma->refresh();
    }
}
