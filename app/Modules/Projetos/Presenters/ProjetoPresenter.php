<?php

namespace App\Modules\Projetos\Presenters;

use App\Modules\Projetos\Models\Projeto;

class ProjetoPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function apresentar(Projeto $projeto): array
    {
        return [
            'id' => $projeto->id,
            'nome' => $projeto->nome,
            'codigo' => $projeto->codigo,
            'descricao' => $projeto->descricao,
            'situacao' => $projeto->situacao,
            'situacaoFormatada' => $projeto->situacaoFormatada(),
            'criadoEm' => $projeto->created_at?->toDateTimeString(),
            'turma' => [
                'id' => $projeto->turma?->id,
                'nome' => $projeto->turma?->nome,
                'codigo' => $projeto->turma?->codigo,
                'periodo' => $projeto->turma?->periodo,
            ],
            'termoDeAbertura' => [
                'objetivo' => $projeto->termoDeAbertura?->objetivo,
                'justificativa' => $projeto->termoDeAbertura?->justificativa,
                'restricoes' => $projeto->termoDeAbertura?->restricoes,
                'premissas' => $projeto->termoDeAbertura?->premissas,
                'entregasEsperadas' => $projeto->termoDeAbertura?->entregas_esperadas,
            ],
        ];
    }
}
