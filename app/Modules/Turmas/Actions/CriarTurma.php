<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\Turma;
use App\Modules\Turmas\Support\GeradorDeCodigoDaTurma;

class CriarTurma
{
    public function __construct(private readonly GeradorDeCodigoDaTurma $geradorDeCodigo) {}

    /**
     * @param  array{nome: string, periodo: string, ano: int|string, descricao?: ?string}  $dados
     */
    public function executar(array $dados): Turma
    {
        return Turma::create([
            'nome' => $dados['nome'],
            'codigo' => $this->geradorDeCodigo->gerar((int) $dados['ano'], $dados['periodo']),
            'periodo' => $dados['periodo'],
            'ano' => (int) $dados['ano'],
            'descricao' => $dados['descricao'] ?? null,
            'aceita_novos_cadastros' => true,
        ]);
    }
}
