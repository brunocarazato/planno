<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;

class CriarTurma
{
    /**
     * @param  array{nome: string, codigo: string, periodo?: ?string, descricao?: ?string}  $dados
     */
    public function executar(array $dados): Turma
    {
        return Turma::create([
            'nome' => $dados['nome'],
            'codigo' => mb_strtoupper($dados['codigo']),
            'periodo' => $dados['periodo'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'aceita_novos_cadastros' => true,
        ]);
    }
}
