<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;

class AtualizarTurma
{
    /**
     * @param  array{nome: string, periodo: string, ano: int|string, descricao?: ?string}  $dados
     */
    public function executar(Turma $turma, array $dados): Turma
    {
        $turma->update([
            'nome' => $dados['nome'],
            'periodo' => $dados['periodo'],
            'ano' => (int) $dados['ano'],
            'descricao' => $dados['descricao'] ?? null,
        ]);

        return $turma->refresh();
    }
}
