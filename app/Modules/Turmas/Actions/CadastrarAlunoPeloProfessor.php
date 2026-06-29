<?php

namespace App\Modules\Turmas\Actions;

use App\Models\User;
use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CadastrarAlunoPeloProfessor
{
    /**
     * @param  array{turma_id: int, nome: string, ra: string, password: string}  $dados
     */
    public function executar(array $dados): CadastroAluno
    {
        return DB::transaction(function () use ($dados): CadastroAluno {
            $ra = mb_strtoupper($dados['ra']);

            $usuario = User::create([
                'name' => $dados['nome'],
                'email' => mb_strtolower($ra).'@alunos.planno.local',
                'ra' => $ra,
                'tipo' => User::TIPO_ALUNO,
                'password' => $dados['password'],
            ]);

            return CadastroAluno::create([
                'user_id' => $usuario->id,
                'turma_id' => $dados['turma_id'],
                'nome' => $dados['nome'],
                'ra' => $ra,
                'status' => CadastroAluno::STATUS_APROVADO,
                'avaliado_em' => Carbon::now(),
                'valido_ate' => Carbon::today()->addYear(),
            ]);
        });
    }
}
