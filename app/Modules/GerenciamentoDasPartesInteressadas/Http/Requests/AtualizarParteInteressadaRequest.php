<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Http\Requests;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;

class AtualizarParteInteressadaRequest extends CadastrarParteInteressadaRequest
{
    public function authorize(): bool
    {
        $parteInteressada = $this->route('parteInteressada');
        $projeto = $this->route('projeto');

        return parent::authorize()
            && $parteInteressada instanceof ParteInteressada
            && $parteInteressada->projeto_id === $projeto?->id;
    }
}
