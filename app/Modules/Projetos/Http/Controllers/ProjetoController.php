<?php

namespace App\Modules\Projetos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Projetos\Actions\AtualizarTermoDeAberturaDoProjeto;
use App\Modules\Projetos\Actions\CriarProjeto;
use App\Modules\Projetos\Http\Requests\AtualizarTermoDeAberturaDoProjetoRequest;
use App\Modules\Projetos\Http\Requests\CriarProjetoRequest;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Presenters\ProjetoPresenter;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjetoController extends Controller
{
    public function index(Request $request, ProjetoPresenter $presenter): Response
    {
        $turmasIdsDoAluno = $this->turmasIdsDoAluno($request->user());

        $projetosPermitidos = Projeto::query()
            ->when(
                $turmasIdsDoAluno !== null,
                fn (Builder $query) => $query->whereIn('turma_id', $turmasIdsDoAluno),
            );

        $projetos = (clone $projetosPermitidos)
            ->with(['turma', 'termoDeAbertura'])
            ->latest()
            ->get()
            ->map(fn (Projeto $projeto) => $presenter->apresentar($projeto));

        $turmas = Turma::query()
            ->ativas()
            ->when(
                $turmasIdsDoAluno !== null,
                fn (Builder $query) => $query->whereIn('id', $turmasIdsDoAluno),
            )
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
                'total' => (clone $projetosPermitidos)->count(),
                'emIniciacao' => (clone $projetosPermitidos)
                    ->where('situacao', Projeto::SITUACAO_EM_INICIACAO)
                    ->count(),
                'turmasAtivas' => Turma::query()
                    ->ativas()
                    ->when(
                        $turmasIdsDoAluno !== null,
                        fn (Builder $query) => $query->whereIn('id', $turmasIdsDoAluno),
                    )
                    ->count(),
            ],
        ]);
    }

    public function store(CriarProjetoRequest $request, CriarProjeto $criarProjeto): RedirectResponse
    {
        $projeto = $criarProjeto->executar($request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Request $request, Projeto $projeto, ProjetoPresenter $presenter): Response
    {
        $this->garantirAcessoAoProjeto($request, $projeto);

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
        $this->garantirAcessoAoProjeto($request, $projeto);

        $atualizarTermo->executar($projeto, $request->validated());

        return to_route('projetos.show', $projeto)->with('success', 'Termo de abertura atualizado.');
    }

    /**
     * @return list<int>|null
     */
    private function turmasIdsDoAluno(?User $usuario): ?array
    {
        if (! $usuario?->aluno()) {
            return null;
        }

        return CadastroAluno::query()
            ->where('user_id', $usuario->id)
            ->aprovados()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valido_ate')
                    ->orWhereDate('valido_ate', '>=', today());
            })
            ->pluck('turma_id')
            ->all();
    }

    private function garantirAcessoAoProjeto(Request $request, Projeto $projeto): void
    {
        $usuario = $request->user();

        if (! $usuario?->aluno()) {
            return;
        }

        abort_unless(
            CadastroAluno::query()
                ->where('user_id', $usuario->id)
                ->where('turma_id', $projeto->turma_id)
                ->aprovados()
                ->where(function (Builder $query): void {
                    $query
                        ->whereNull('valido_ate')
                        ->orWhereDate('valido_ate', '>=', today());
                })
                ->exists(),
            403,
        );
    }
}
