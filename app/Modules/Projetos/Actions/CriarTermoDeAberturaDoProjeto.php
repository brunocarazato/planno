<?php

namespace App\Modules\Projetos\Actions;

use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Models\TermoDeAbertura;

class CriarTermoDeAberturaDoProjeto
{
    public function executar(Projeto $projeto): TermoDeAbertura
    {
        return $projeto->termoDeAbertura()->firstOrCreate();
    }
}
