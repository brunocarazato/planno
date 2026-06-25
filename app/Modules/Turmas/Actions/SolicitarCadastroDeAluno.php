<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\CadastroAluno;

class SolicitarCadastroDeAluno
{
    /**
     * @param  array{turma_id: int, nome: string, ra: string}  $dados
     */
    public function executar(array $dados): CadastroAluno
    {
        return CadastroAluno::create([
            'turma_id' => $dados['turma_id'],
            'nome' => $dados['nome'],
            'ra' => mb_strtoupper($dados['ra']),
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);
    }
}
