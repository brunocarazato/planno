<?php

namespace App\Modules\GerenciamentoDeEscopo\Http\Requests;

use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;

class AtualizarDeclaracaoDeEscopoRequest extends SalvarDeclaracaoDeEscopoRequest
{
    public function authorize(): bool
    {
        $projeto = $this->route('projeto');
        $declaracao = $this->route('declaracaoDeEscopo');

        return parent::authorize()
            && $declaracao instanceof DeclaracaoDeEscopo
            && $declaracao->projeto_id === $projeto?->id;
    }
}
