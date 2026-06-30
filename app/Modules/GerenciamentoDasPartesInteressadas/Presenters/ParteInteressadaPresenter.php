<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Presenters;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;

class ParteInteressadaPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function apresentar(ParteInteressada $parteInteressada): array
    {
        return [
            'id' => $parteInteressada->id,
            'nome' => $parteInteressada->nome,
            'papel' => $parteInteressada->papel,
            'organizacao' => $parteInteressada->organizacao,
            'poder' => $parteInteressada->poder,
            'poderFormatado' => $parteInteressada->poderFormatado(),
            'interesse' => $parteInteressada->interesse,
            'interesseFormatado' => $parteInteressada->interesseFormatado(),
            'estrategiaEngajamento' => $parteInteressada->estrategia_engajamento,
        ];
    }
}
