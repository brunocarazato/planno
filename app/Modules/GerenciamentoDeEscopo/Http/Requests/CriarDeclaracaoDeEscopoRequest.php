<?php

namespace App\Modules\GerenciamentoDeEscopo\Http\Requests;

class CriarDeclaracaoDeEscopoRequest extends SalvarDeclaracaoDeEscopoRequest
{
    public function authorize(): bool
    {
        $projeto = $this->route('projeto');

        return parent::authorize() && $projeto?->declaracaoDeEscopo()->doesntExist();
    }
}
