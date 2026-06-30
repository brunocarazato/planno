<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GerenciamentoDasPartesInteressadas\Actions\AtualizarParteInteressada;
use App\Modules\GerenciamentoDasPartesInteressadas\Actions\CadastrarParteInteressada;
use App\Modules\GerenciamentoDasPartesInteressadas\Actions\ExcluirParteInteressada;
use App\Modules\GerenciamentoDasPartesInteressadas\Http\Requests\AtualizarParteInteressadaRequest;
use App\Modules\GerenciamentoDasPartesInteressadas\Http\Requests\CadastrarParteInteressadaRequest;
use App\Modules\GerenciamentoDasPartesInteressadas\Http\Requests\ExcluirParteInteressadaRequest;
use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;
use App\Modules\Projetos\Models\Projeto;
use Illuminate\Http\RedirectResponse;

class ParteInteressadaController extends Controller
{
    public function store(
        CadastrarParteInteressadaRequest $request,
        Projeto $projeto,
        CadastrarParteInteressada $cadastrarParteInteressada,
    ): RedirectResponse {
        $cadastrarParteInteressada->executar($projeto, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Parte interessada cadastrada.');
    }

    public function update(
        AtualizarParteInteressadaRequest $request,
        Projeto $projeto,
        ParteInteressada $parteInteressada,
        AtualizarParteInteressada $atualizarParteInteressada,
    ): RedirectResponse {
        $atualizarParteInteressada->executar($parteInteressada, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Parte interessada atualizada.');
    }

    public function destroy(
        ExcluirParteInteressadaRequest $request,
        Projeto $projeto,
        ParteInteressada $parteInteressada,
        ExcluirParteInteressada $excluirParteInteressada,
    ): RedirectResponse {
        $excluirParteInteressada->executar($parteInteressada);

        return to_route('projetos.show', $projeto)->with('success', 'Parte interessada removida.');
    }
}
