<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;

class AtualizarTurma
{
    /**
     * @param  array{nome: string, codigo: string, periodo?: ?string, descricao?: ?string}  $dados
     */
    public function executar(Turma $turma, array $dados): Turma
    {
        $turma->update([
            'nome' => $dados['nome'],
            'codigo' => mb_strtoupper($dados['codigo']),
            'periodo' => $dados['periodo'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
        ]);

        return $turma->refresh();
    }
}
