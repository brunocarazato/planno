<?php

namespace App\Modules\Projetos\Actions;

use App\Models\User;
use App\Modules\GruposDeProcessos\Actions\IniciarTrilhaDoProjeto;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Support\GeradorDeCodigoDoProjeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Support\Facades\DB;

class CriarProjeto
{
    public function __construct(
        private readonly CriarTermoDeAberturaDoProjeto $criarTermoDeAbertura,
        private readonly GeradorDeCodigoDoProjeto $geradorDeCodigo,
        private readonly IniciarTrilhaDoProjeto $iniciarTrilha,
    ) {}

    /**
     * @param  array{turma_id: int, nome: string, descricao?: string|null}  $dados
     */
    public function executar(array $dados, User $responsavel): Projeto
    {
        return DB::transaction(function () use ($dados, $responsavel): Projeto {
            $turma = Turma::query()->lockForUpdate()->findOrFail($dados['turma_id']);

            $projeto = Projeto::create([
                'turma_id' => $turma->id,
                'responsavel_id' => $responsavel->id,
                'nome' => $dados['nome'],
                'codigo' => $this->geradorDeCodigo->gerar($turma),
                'descricao' => $dados['descricao'] ?? null,
                'situacao' => Projeto::SITUACAO_EM_INICIACAO,
            ]);

            $this->criarTermoDeAbertura->executar($projeto);
            $this->iniciarTrilha->executar($projeto);

            return $projeto;
        });
    }
}
