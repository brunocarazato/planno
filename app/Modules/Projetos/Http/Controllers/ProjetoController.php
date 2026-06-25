<?php

namespace App\Modules\Projetos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Projetos\Actions\AtualizarTermoDeAberturaDoProjeto;
use App\Modules\Projetos\Actions\CriarProjeto;
use App\Modules\Projetos\Http\Requests\AtualizarTermoDeAberturaDoProjetoRequest;
use App\Modules\Projetos\Http\Requests\CriarProjetoRequest;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Presenters\ProjetoPresenter;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjetoController extends Controller
{
    public function index(ProjetoPresenter $presenter): Response
    {
        $projetos = Projeto::query()
            ->with(['turma', 'termoDeAbertura'])
            ->latest()
            ->get()
            ->map(fn (Projeto $projeto) => $presenter->apresentar($projeto));

        $turmas = Turma::query()
            ->ativas()
            ->orderBy('nome')
            ->get()
            ->map(fn (Turma $turma) => [
                'id' => $turma->id,
                'nome' => $turma->nome,
                'codigo' => $turma->codigo,
                'periodo' => $turma->periodo,
            ]);

        return Inertia::render('Projetos/Index', [
            'projetos' => $projetos,
            'turmas' => $turmas,
            'metricas' => [
                'total' => Projeto::query()->count(),
                'emIniciacao' => Projeto::query()
                    ->where('situacao', Projeto::SITUACAO_EM_INICIACAO)
                    ->count(),
                'turmasAtivas' => Turma::query()->ativas()->count(),
            ],
        ]);
    }

    public function store(CriarProjetoRequest $request, CriarProjeto $criarProjeto): RedirectResponse
    {
        $projeto = $criarProjeto->executar($request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Projeto $projeto, ProjetoPresenter $presenter): Response
    {
        $projeto->load(['turma', 'termoDeAbertura']);

        return Inertia::render('Projetos/Show', [
            'projeto' => $presenter->apresentar($projeto),
        ]);
    }

    public function atualizarTermoDeAbertura(
        AtualizarTermoDeAberturaDoProjetoRequest $request,
        Projeto $projeto,
        AtualizarTermoDeAberturaDoProjeto $atualizarTermo,
    ): RedirectResponse {
        $atualizarTermo->executar($projeto, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Termo de abertura atualizado.');
    }
}
