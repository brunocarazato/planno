<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;

class BloquearNovosCadastrosNaTurma
{
    public function executar(Turma $turma): Turma
    {
        $turma->update(['aceita_novos_cadastros' => false]);

        return $turma->refresh();
    }
}
