<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Support\Carbon;

class AprovarCadastroDeAluno
{
    public function executar(CadastroAluno $cadastroAluno): CadastroAluno
    {
        if (! $cadastroAluno->estaPendente()) {
            return $cadastroAluno;
        }

        $cadastroAluno->update([
            'status' => CadastroAluno::STATUS_APROVADO,
            'motivo_reprovacao' => null,
            'avaliado_em' => Carbon::now(),
            'valido_ate' => Carbon::today()->addYear(),
        ]);

        return $cadastroAluno->refresh();
    }
}
