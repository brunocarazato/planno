<?php

namespace App\Modules\Turmas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Turmas\Actions\ArquivarTurma;
use App\Modules\Turmas\Actions\AtualizarTurma;
use App\Modules\Turmas\Actions\BloquearNovosCadastrosNaTurma;
use App\Modules\Turmas\Actions\CriarTurma;
use App\Modules\Turmas\Actions\PermitirNovosCadastrosNaTurma;
use App\Modules\Turmas\Http\Requests\AtualizarTurmaRequest;
use App\Modules\Turmas\Http\Requests\CriarTurmaRequest;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use App\Modules\Turmas\Presenters\CadastroAlunoPresenter;
use App\Modules\Turmas\Presenters\TurmaPresenter;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TurmaController extends Controller
{
    public function index(TurmaPresenter $presenter, CadastroAlunoPresenter $cadastroPresenter): Response
    {
        $turmas = Turma::query()
            ->latest()
            ->get()
            ->map(fn (Turma $turma) => $presenter->apresentar($turma));

        $cadastrosPendentes = CadastroAluno::query()
            ->with('turma')
            ->pendentes()
            ->latest()
            ->get()
            ->map(fn (CadastroAluno $cadastroAluno) => $cadastroPresenter->apresentar($cadastroAluno));

        return Inertia::render('Turmas/Index', [
            'turmas' => $turmas,
            'cadastrosPendentes' => $cadastrosPendentes,
            'metricas' => [
                'total' => Turma::query()->count(),
                'ativas' => Turma::query()->ativas()->count(),
                'aceitandoCadastros' => Turma::query()
                    ->ativas()
                    ->where('aceita_novos_cadastros', true)
                    ->count(),
                'cadastrosPendentes' => CadastroAluno::query()->pendentes()->count(),
            ],
        ]);
    }

    public function store(CriarTurmaRequest $request, CriarTurma $criarTurma): RedirectResponse
    {
        $criarTurma->executar($request->validated());

        return to_route('turmas.index')->with('success', 'Turma criada com sucesso.');
    }

    public function update(
        AtualizarTurmaRequest $request,
        Turma $turma,
        AtualizarTurma $atualizarTurma,
    ): RedirectResponse {
        $atualizarTurma->executar($turma, $request->validated());

        return to_route('turmas.index')->with('success', 'Turma atualizada com sucesso.');
    }

    public function permitirCadastros(
        Turma $turma,
        PermitirNovosCadastrosNaTurma $permitirNovosCadastros,
    ): RedirectResponse {
        $permitirNovosCadastros->executar($turma);

        return to_route('turmas.index')->with('success', 'Novos cadastros foram permitidos.');
    }

    public function bloquearCadastros(
        Turma $turma,
        BloquearNovosCadastrosNaTurma $bloquearNovosCadastros,
    ): RedirectResponse {
        $bloquearNovosCadastros->executar($turma);

        return to_route('turmas.index')->with('success', 'Novos cadastros foram bloqueados.');
    }

    public function arquivar(Turma $turma, ArquivarTurma $arquivarTurma): RedirectResponse
    {
        $arquivarTurma->executar($turma);

        return to_route('turmas.index')->with('success', 'Turma arquivada com sucesso.');
    }
}
