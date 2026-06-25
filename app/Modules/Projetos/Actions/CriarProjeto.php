<?php

namespace App\Modules\Projetos\Actions;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Support\Facades\DB;

class CriarProjeto
{
    public function __construct(private readonly CriarTermoDeAberturaDoProjeto $criarTermoDeAbertura) {}

    /**
     * @param  array{turma_id: int, nome: string, codigo: string, descricao?: string|null}  $dados
     */
    public function executar(array $dados): Projeto
    {
        return DB::transaction(function () use ($dados): Projeto {
            $projeto = Projeto::create([
                'turma_id' => $dados['turma_id'],
                'nome' => $dados['nome'],
                'codigo' => mb_strtoupper($dados['codigo']),
                'descricao' => $dados['descricao'] ?? null,
                'situacao' => Projeto::SITUACAO_EM_INICIACAO,
            ]);

            $this->criarTermoDeAbertura->executar($projeto);

            return $projeto;
        });
    }
}
