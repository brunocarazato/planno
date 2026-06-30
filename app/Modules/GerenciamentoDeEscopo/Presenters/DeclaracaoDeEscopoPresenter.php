<?php

namespace App\Modules\GerenciamentoDeEscopo\Presenters;

use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;

class DeclaracaoDeEscopoPresenter
{
    /**
     * @return array{id: int, descricao: string|null, inclui: string|null, exclusoes: string|null}
     */
    public function apresentar(DeclaracaoDeEscopo $declaracao): array
    {
        return [
            'id' => $declaracao->id,
            'descricao' => $declaracao->descricao,
            'inclui' => $declaracao->inclui,
            'exclusoes' => $declaracao->exclusoes,
        ];
    }
}
