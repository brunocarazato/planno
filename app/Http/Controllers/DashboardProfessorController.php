<?php

namespace App\Http\Controllers;

use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class DashboardProfessorController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard/Professor', [
            'metricas' => [
                'projetos' => [
                    'total' => Projeto::query()->count(),
                    'emIniciacao' => Projeto::query()
                        ->where('situacao', Projeto::SITUACAO_EM_INICIACAO)
                        ->count(),
                ],
                'turmas' => [
                    'total' => Turma::query()->count(),
                    'ativas' => Turma::query()->ativas()->count(),
                    'aceitandoCadastros' => Turma::query()
                        ->ativas()
                        ->where('aceita_novos_cadastros', true)
                        ->count(),
                ],
                'alunos' => [
                    'cadastros' => CadastroAluno::query()->count(),
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
            ],
        ]);
    }
}
