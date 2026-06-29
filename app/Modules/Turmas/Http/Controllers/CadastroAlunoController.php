<?php

namespace App\Modules\Turmas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Turmas\Actions\AprovarCadastroDeAluno;
use App\Modules\Turmas\Actions\CadastrarAlunoPeloProfessor;
use App\Modules\Turmas\Actions\ReprovarCadastroDeAluno;
use App\Modules\Turmas\Actions\SolicitarCadastroDeAluno;
use App\Modules\Turmas\Http\Requests\CadastrarAlunoPeloProfessorRequest;
use App\Modules\Turmas\Http\Requests\ReprovarCadastroDeAlunoRequest;
use App\Modules\Turmas\Http\Requests\SolicitarCadastroDeAlunoRequest;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use App\Modules\Turmas\Presenters\CadastroAlunoPresenter;
use App\Modules\Turmas\Presenters\TurmaPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CadastroAlunoController extends Controller
{
    public function index(CadastroAlunoPresenter $cadastroPresenter, TurmaPresenter $turmaPresenter): Response
    {
        $cadastros = CadastroAluno::query()
            ->with('turma')
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [CadastroAluno::STATUS_PENDENTE])
            ->latest()
            ->get()
            ->map(fn (CadastroAluno $cadastroAluno) => $cadastroPresenter->apresentar($cadastroAluno));

        $turmas = Turma::query()
            ->orderBy('nome')
            ->get()
            ->map(fn (Turma $turma) => $turmaPresenter->apresentar($turma));

        $turmasAtivas = $turmas
            ->whereNull('arquivadaEm')
            ->values();

        return Inertia::render('Alunos/Index', [
            'cadastros' => $cadastros,
            'turmas' => $turmas,
            'turmasAtivas' => $turmasAtivas,
            'metricas' => [
                'total' => CadastroAluno::query()->count(),
                'pendentes' => CadastroAluno::query()->pendentes()->count(),
                'aprovadosAtivos' => CadastroAluno::query()
                    ->aprovados()
                    ->where(function (Builder $query): void {
                        $query
                            ->whereNull('valido_ate')
                            ->orWhereDate('valido_ate', '>=', today());
                    })
                    ->count(),
            ],
        ]);
    }

    public function create(Request $request): Response|RedirectResponse
    {
        if ($request->user()) {
            return $request->user()->professor()
                ? to_route('alunos.index')
                : to_route('projetos.index');
        }

        $turmas = Turma::query()
            ->ativas()
            ->where('aceita_novos_cadastros', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'codigo', 'periodo', 'ano'])
            ->map(fn (Turma $turma) => [
                'id' => $turma->id,
                'nome' => $turma->nome,
                'codigo' => $turma->codigo,
                'periodo' => $turma->periodo,
                'ano' => $turma->ano,
                'periodoFormatado' => $turma->periodoFormatado(),
            ]);

        return Inertia::render('CadastrosAlunos/Solicitar', [
            'turmas' => $turmas,
        ]);
    }

    public function store(
        SolicitarCadastroDeAlunoRequest $request,
        SolicitarCadastroDeAluno $solicitarCadastro,
    ): RedirectResponse {
        $solicitarCadastro->executar($request->validated());

        return to_route('cadastros-alunos.create')
            ->with('success', 'Cadastro solicitado com sucesso. Aguarde a aprovação do professor.');
    }

    public function cadastrar(
        CadastrarAlunoPeloProfessorRequest $request,
        CadastrarAlunoPeloProfessor $cadastrarAluno,
    ): RedirectResponse {
        $cadastrarAluno->executar($request->validated());

        return to_route('alunos.index')
            ->with('success', 'Aluno cadastrado e aprovado com validade de 1 ano.');
    }

    public function aprovar(
        CadastroAluno $cadastroAluno,
        AprovarCadastroDeAluno $aprovarCadastro,
    ): RedirectResponse {
        $aprovarCadastro->executar($cadastroAluno);

        return to_route('alunos.index')->with('success', 'Cadastro de aluno aprovado com validade de 1 ano.');
    }

    public function reprovar(
        ReprovarCadastroDeAlunoRequest $request,
        CadastroAluno $cadastroAluno,
        ReprovarCadastroDeAluno $reprovarCadastro,
    ): RedirectResponse {
        $reprovarCadastro->executar($cadastroAluno, $request->validated('motivo_reprovacao'));

        return to_route('alunos.index')->with('success', 'Cadastro de aluno reprovado.');
    }
}
