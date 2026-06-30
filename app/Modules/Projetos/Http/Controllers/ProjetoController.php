<?php

namespace App\Modules\Projetos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\GruposDeProcessos\Actions\IniciarTrilhaDoProjeto;
use App\Modules\GruposDeProcessos\Presenters\TrilhaDoProjetoPresenter;
use App\Modules\Projetos\Actions\AtualizarProjeto;
use App\Modules\Projetos\Actions\AtualizarResponsavelDoProjeto;
use App\Modules\Projetos\Actions\AtualizarTermoDeAberturaDoProjeto;
use App\Modules\Projetos\Actions\CriarProjeto;
use App\Modules\Projetos\Http\Requests\AtualizarProjetoRequest;
use App\Modules\Projetos\Http\Requests\AtualizarResponsavelDoProjetoRequest;
use App\Modules\Projetos\Http\Requests\AtualizarTermoDeAberturaDoProjetoRequest;
use App\Modules\Projetos\Http\Requests\CriarProjetoRequest;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Presenters\ProjetoPresenter;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProjetoController extends Controller
{
    public function index(Request $request, ProjetoPresenter $presenter): Response
    {
        $usuario = $request->user();
        $turmasIdsDoAluno = $this->turmasIdsDoAluno($request->user());

        $projetosPermitidos = Projeto::query()
            ->when(
                $usuario?->aluno() === true,
                fn (Builder $query) => $query->where('responsavel_id', $usuario?->id),
            );

        $projetos = (clone $projetosPermitidos)
            ->with(['turma', 'responsavel', 'termoDeAbertura'])
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
                'ano' => $turma->ano,
                'periodoFormatado' => $turma->periodoFormatado(),
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
        $usuario = $request->user();

        abort_unless($usuario instanceof User, 403);

        $projeto = $criarProjeto->executar($request->validated(), $usuario);

        return to_route('projetos.show', $projeto)->with('success', 'Projeto criado com sucesso.');
    }

    public function show(
        Request $request,
        Projeto $projeto,
        ProjetoPresenter $presenter,
        IniciarTrilhaDoProjeto $iniciarTrilha,
        TrilhaDoProjetoPresenter $trilhaPresenter,
    ): Response {
        $this->garantirAcessoAoProjeto($request, $projeto);

        $projeto->load(['turma', 'responsavel', 'termoDeAbertura']);
        $trilha = $iniciarTrilha->executar($projeto);
        $trilha->load('conclusoes.autorDaConclusao');

        return Inertia::render('Projetos/Show', [
            'projeto' => $presenter->apresentar($projeto),
            'trilha' => $trilhaPresenter->apresentar($trilha),
            'podeAlterarResponsavel' => $request->user()?->professor() === true,
            'responsaveisDisponiveis' => $request->user()?->professor() === true
                ? $this->responsaveisDisponiveis($projeto)
                : [],
        ]);
    }

    public function update(
        AtualizarProjetoRequest $request,
        Projeto $projeto,
        AtualizarProjeto $atualizarProjeto,
        AtualizarTermoDeAberturaDoProjeto $atualizarTermo,
        AtualizarResponsavelDoProjeto $atualizarResponsavel,
    ): RedirectResponse {
        $dados = $request->validated();

        DB::transaction(function () use (
            $projeto,
            $dados,
            $atualizarProjeto,
            $atualizarTermo,
            $atualizarResponsavel,
        ): void {
            $atualizarProjeto->executar($projeto, Arr::only($dados, ['nome', 'descricao']));

            $camposDoTermo = Arr::only($dados, [
                'objetivo',
                'justificativa',
                'restricoes',
                'premissas',
                'entregas_esperadas',
            ]);

            if ($camposDoTermo !== []) {
                $atualizarTermo->executar($projeto, $camposDoTermo);
            }

            if (array_key_exists('responsavel_id', $dados)) {
                $responsavel = User::findOrFail($dados['responsavel_id']);
                $atualizarResponsavel->executar($projeto, $responsavel);
            }
        });

        return to_route('projetos.show', $projeto)->with('success', 'Alterações do projeto salvas com sucesso.');
    }

    public function atualizarResponsavel(
        AtualizarResponsavelDoProjetoRequest $request,
        Projeto $projeto,
        AtualizarResponsavelDoProjeto $atualizarResponsavel,
    ): RedirectResponse {
        $responsavel = User::findOrFail($request->integer('responsavel_id'));

        $atualizarResponsavel->executar($projeto, $responsavel);

        return to_route('projetos.show', $projeto)->with('success', 'Responsável do projeto atualizado.');
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
            $projeto->responsavel_id === $usuario->id,
            403,
        );
    }

    /**
     * @return array<int, array{id: int, name: string, ra: string|null, tipo: string}>
     */
    private function responsaveisDisponiveis(Projeto $projeto): array
    {
        $alunosIds = CadastroAluno::query()
            ->where('turma_id', $projeto->turma_id)
            ->aprovados()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valido_ate')
                    ->orWhereDate('valido_ate', '>=', today());
            })
            ->pluck('user_id')
            ->filter()
            ->values()
            ->all();

        return User::query()
            ->where('tipo', User::TIPO_PROFESSOR)
            ->orWhereIn('id', $alunosIds)
            ->orderBy('name')
            ->get()
            ->map(fn (User $usuario) => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'ra' => $usuario->ra,
                'tipo' => $usuario->tipo,
            ])
            ->all();
    }
}
