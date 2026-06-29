<?php

namespace App\Modules\GruposDeProcessos\Actions;

use App\Modules\GruposDeProcessos\Models\TrilhaDoProjeto;
use App\Modules\Projetos\Models\Projeto;

class IniciarTrilhaDoProjeto
{
    public function executar(Projeto $projeto): TrilhaDoProjeto
    {
        return $projeto->trilhaDosGruposDeProcessos()->firstOrCreate();
    }
}
