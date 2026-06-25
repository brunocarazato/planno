<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;

class PermitirNovosCadastrosNaTurma
{
    public function executar(Turma $turma): Turma
    {
        if ($turma->estaArquivada()) {
            return $turma;
        }

        $turma->update(['aceita_novos_cadastros' => true]);

        return $turma->refresh();
    }
}
