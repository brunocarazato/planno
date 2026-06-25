<?php

namespace App\Modules\Turmas\Presenters;

use App\Modules\Turmas\Models\CadastroAluno;

class CadastroAlunoPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function apresentar(CadastroAluno $cadastroAluno): array
    {
        return [
            'id' => $cadastroAluno->id,
            'nome' => $cadastroAluno->nome,
            'ra' => $cadastroAluno->ra,
            'status' => $cadastroAluno->status,
            'motivoReprovacao' => $cadastroAluno->motivo_reprovacao,
            'avaliadoEm' => $cadastroAluno->avaliado_em?->toDateTimeString(),
            'validoAte' => $cadastroAluno->valido_ate?->toDateString(),
            'participacaoAtivaPermitida' => $cadastroAluno->permiteParticipacaoAtiva(),
            'turma' => [
                'id' => $cadastroAluno->turma?->id,
                'nome' => $cadastroAluno->turma?->nome,
                'codigo' => $cadastroAluno->turma?->codigo,
            ],
            'criadoEm' => $cadastroAluno->created_at?->toDateTimeString(),
        ];
    }
}
