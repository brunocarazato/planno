<?php

namespace App\Modules\Projetos\Actions;

use App\Modules\Projetos\Models\Projeto;

class AtualizarProjeto
{
    /**
     * @param  array{nome: string, descricao?: string|null}  $dados
     */
    public function executar(Projeto $projeto, array $dados): Projeto
    {
        $projeto->update([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
        ]);

        return $projeto->refresh();
    }
}
