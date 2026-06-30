<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Actions;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;

class AtualizarParteInteressada
{
    /**
     * @param  array{nome: string, papel?: string|null, organizacao?: string|null, poder: string, interesse: string, estrategia_engajamento?: string|null}  $dados
     */
    public function executar(ParteInteressada $parteInteressada, array $dados): ParteInteressada
    {
        $parteInteressada->update($dados);

        return $parteInteressada->refresh();
    }
}
