<?php

namespace App\Modules\Projetos\Actions;

use App\Models\User;
use App\Modules\Projetos\Models\Projeto;

class AtualizarResponsavelDoProjeto
{
    public function executar(Projeto $projeto, User $responsavel): Projeto
    {
        $projeto->update([
            'responsavel_id' => $responsavel->id,
        ]);

        return $projeto->refresh();
    }
}
