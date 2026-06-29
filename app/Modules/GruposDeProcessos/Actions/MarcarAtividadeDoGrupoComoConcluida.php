<?php

namespace App\Modules\GruposDeProcessos\Actions;

use App\Models\User;
use App\Modules\GruposDeProcessos\Models\TrilhaDoProjeto;
use App\Modules\GruposDeProcessos\Support\CatalogoDaTrilhaDosGruposDeProcessos;
use Illuminate\Validation\ValidationException;

class MarcarAtividadeDoGrupoComoConcluida
{
    public function __construct(private CatalogoDaTrilhaDosGruposDeProcessos $catalogo) {}

    public function executar(
        TrilhaDoProjeto $trilha,
        string $chaveDaAtividade,
        bool $concluida,
        User $usuario,
    ): void {
        if (! $this->catalogo->possuiAtividade($chaveDaAtividade)) {
            throw ValidationException::withMessages([
                'atividade' => 'A atividade informada não pertence à trilha do projeto.',
            ]);
        }

        if (! $concluida) {
            $trilha->conclusoes()->where('chave_atividade', $chaveDaAtividade)->delete();

            return;
        }

        $trilha->conclusoes()->updateOrCreate(
            ['chave_atividade' => $chaveDaAtividade],
            [
                'concluida_por' => $usuario->id,
                'concluida_em' => now(),
            ],
        );
    }
}
