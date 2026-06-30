<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Actions;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;
use App\Modules\Projetos\Models\Projeto;

class CadastrarParteInteressada
{
    /**
     * @param  array{nome: string, papel?: string|null, organizacao?: string|null, poder: string, interesse: string, estrategia_engajamento?: string|null}  $dados
     */
    public function executar(Projeto $projeto, array $dados): ParteInteressada
    {
        return $projeto->partesInteressadas()->create($dados);
    }
}
