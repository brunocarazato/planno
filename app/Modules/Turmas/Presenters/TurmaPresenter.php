<?php

namespace App\Modules\Turmas\Presenters;

use App\Modules\Turmas\Models\Turma;

class TurmaPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function apresentar(Turma $turma): array
    {
        return [
            'id' => $turma->id,
            'nome' => $turma->nome,
            'codigo' => $turma->codigo,
            'periodo' => $turma->periodo,
            'descricao' => $turma->descricao,
            'aceitaNovosCadastros' => $turma->aceita_novos_cadastros,
            'arquivadaEm' => $turma->arquivada_em?->toDateTimeString(),
            'status' => $turma->estaArquivada()
                ? 'Arquivada'
                : ($turma->aceita_novos_cadastros ? 'Aceitando cadastros' : 'Cadastros bloqueados'),
            'criadaEm' => $turma->created_at?->toDateTimeString(),
        ];
    }
}
