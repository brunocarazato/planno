<?php

namespace App\Modules\GerenciamentoDeEscopo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GerenciamentoDeEscopo\Actions\AtualizarDeclaracaoDeEscopo;
use App\Modules\GerenciamentoDeEscopo\Actions\CriarDeclaracaoDeEscopo;
use App\Modules\GerenciamentoDeEscopo\Http\Requests\AtualizarDeclaracaoDeEscopoRequest;
use App\Modules\GerenciamentoDeEscopo\Http\Requests\CriarDeclaracaoDeEscopoRequest;
use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;
use App\Modules\Projetos\Models\Projeto;
use Illuminate\Http\RedirectResponse;

class DeclaracaoDeEscopoController extends Controller
{
    public function store(
        CriarDeclaracaoDeEscopoRequest $request,
        Projeto $projeto,
        CriarDeclaracaoDeEscopo $criarDeclaracao,
    ): RedirectResponse {
        $criarDeclaracao->executar($projeto, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Declaração de escopo criada.');
    }

    public function update(
        AtualizarDeclaracaoDeEscopoRequest $request,
        Projeto $projeto,
        DeclaracaoDeEscopo $declaracaoDeEscopo,
        AtualizarDeclaracaoDeEscopo $atualizarDeclaracao,
    ): RedirectResponse {
        $atualizarDeclaracao->executar($declaracaoDeEscopo, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Declaração de escopo atualizada.');
    }
}
