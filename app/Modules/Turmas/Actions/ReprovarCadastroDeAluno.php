<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Support\Carbon;

class ReprovarCadastroDeAluno
{
    public function executar(CadastroAluno $cadastroAluno, ?string $motivo = null): CadastroAluno
    {
        if (! $cadastroAluno->estaPendente()) {
            return $cadastroAluno;
        }

        $cadastroAluno->update([
            'status' => CadastroAluno::STATUS_REPROVADO,
            'motivo_reprovacao' => $motivo,
            'avaliado_em' => Carbon::now(),
            'valido_ate' => null,
        ]);

        return $cadastroAluno->refresh();
    }
}
