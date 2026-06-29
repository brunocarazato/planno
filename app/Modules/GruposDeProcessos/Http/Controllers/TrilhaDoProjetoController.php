<?php

namespace App\Modules\GruposDeProcessos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\GruposDeProcessos\Actions\IniciarTrilhaDoProjeto;
use App\Modules\GruposDeProcessos\Actions\MarcarAtividadeDoGrupoComoConcluida;
use App\Modules\GruposDeProcessos\Http\Requests\AtualizarConclusaoDaAtividadeRequest;
use App\Modules\Projetos\Models\Projeto;
use Illuminate\Http\RedirectResponse;

class TrilhaDoProjetoController extends Controller
{
    public function atualizarConclusao(
        AtualizarConclusaoDaAtividadeRequest $request,
        Projeto $projeto,
        string $atividade,
        IniciarTrilhaDoProjeto $iniciarTrilha,
        MarcarAtividadeDoGrupoComoConcluida $marcarAtividade,
    ): RedirectResponse {
        $usuario = $request->user();

        abort_unless($usuario instanceof User, 403);

        $trilha = $iniciarTrilha->executar($projeto);

        $marcarAtividade->executar(
            $trilha,
            $atividade,
            $request->boolean('concluida'),
            $usuario,
        );

        $mensagem = $request->boolean('concluida')
            ? 'Atividade marcada como concluída.'
            : 'Atividade reaberta para revisão.';

        return to_route('projetos.show', $projeto)->with('success', $mensagem);
    }
}
