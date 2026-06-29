<?php

namespace App\Modules\GruposDeProcessos\Presenters;

use App\Modules\GruposDeProcessos\Models\TrilhaDoProjeto;
use App\Modules\GruposDeProcessos\Support\CatalogoDaTrilhaDosGruposDeProcessos;

class TrilhaDoProjetoPresenter
{
    public function __construct(private CatalogoDaTrilhaDosGruposDeProcessos $catalogo) {}

    /**
     * @return array<string, mixed>
     */
    public function apresentar(TrilhaDoProjeto $trilha): array
    {
        $conclusoes = $trilha->conclusoes->keyBy('chave_atividade');
        $totalDeAtividades = 0;
        $totalConcluido = 0;

        $grupos = collect($this->catalogo->grupos())
            ->map(function (array $grupo) use ($conclusoes, &$totalDeAtividades, &$totalConcluido): array {
                $atividades = collect($grupo['atividades'])
                    ->map(function (array $atividade) use ($conclusoes): array {
                        $conclusao = $conclusoes->get($atividade['chave']);

                        return [
                            ...$atividade,
                            'concluida' => $conclusao !== null,
                            'concluidaEm' => $conclusao?->concluida_em?->toDateTimeString(),
                            'concluidaPor' => $conclusao?->autorDaConclusao?->name,
                        ];
                    })
                    ->all();

                $concluidasNoGrupo = collect($atividades)->where('concluida', true)->count();
                $totalNoGrupo = count($atividades);
                $totalDeAtividades += $totalNoGrupo;
                $totalConcluido += $concluidasNoGrupo;

                return [
                    'chave' => $grupo['chave'],
                    'nome' => $grupo['nome'],
                    'descricao' => $grupo['descricao'],
                    'atividades' => $atividades,
                    'progresso' => [
                        'concluidas' => $concluidasNoGrupo,
                        'total' => $totalNoGrupo,
                        'percentual' => $this->percentual($concluidasNoGrupo, $totalNoGrupo),
                    ],
                ];
            })
            ->all();

        return [
            'id' => $trilha->id,
            'grupos' => $grupos,
            'progresso' => [
                'concluidas' => $totalConcluido,
                'total' => $totalDeAtividades,
                'percentual' => $this->percentual($totalConcluido, $totalDeAtividades),
            ],
        ];
    }

    private function percentual(int $concluidas, int $total): int
    {
        return $total === 0 ? 0 : (int) round(($concluidas / $total) * 100);
    }
}
