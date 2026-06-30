<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Actions;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;

class ExcluirParteInteressada
{
    public function executar(ParteInteressada $parteInteressada): void
    {
        $parteInteressada->delete();
    }
}
