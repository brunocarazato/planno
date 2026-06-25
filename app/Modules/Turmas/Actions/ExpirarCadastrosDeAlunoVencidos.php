<?php

namespace App\Modules\Turmas\Actions;

use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Support\Carbon;

class ExpirarCadastrosDeAlunoVencidos
{
    public function executar(): int
    {
        return CadastroAluno::query()
            ->aprovados()
            ->whereDate('valido_ate', '<', Carbon::today())
            ->update(['status' => CadastroAluno::STATUS_EXPIRADO]);
    }
}
