<?php

namespace App\Modules\Turmas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Turmas\Actions\AprovarCadastroDeAluno;
use App\Modules\Turmas\Actions\ReprovarCadastroDeAluno;
use App\Modules\Turmas\Actions\SolicitarCadastroDeAluno;
use App\Modules\Turmas\Http\Requests\ReprovarCadastroDeAlunoRequest;
use App\Modules\Turmas\Http\Requests\SolicitarCadastroDeAlunoRequest;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CadastroAlunoController extends Controller
{
    public function create(): Response
    {
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
            ->with('success', 'Cadastro solicitado com sucesso. Aguarde a aprovacao do professor.');
    }

    public function aprovar(
        CadastroAluno $cadastroAluno,
        AprovarCadastroDeAluno $aprovarCadastro,
    ): RedirectResponse {
        $aprovarCadastro->executar($cadastroAluno);

        return to_route('turmas.index')->with('success', 'Cadastro de aluno aprovado com validade de 1 ano.');
    }

    public function reprovar(
        ReprovarCadastroDeAlunoRequest $request,
        CadastroAluno $cadastroAluno,
        ReprovarCadastroDeAluno $reprovarCadastro,
    ): RedirectResponse {
        $reprovarCadastro->executar($cadastroAluno, $request->validated('motivo_reprovacao'));

        return to_route('turmas.index')->with('success', 'Cadastro de aluno reprovado.');
    }
}
