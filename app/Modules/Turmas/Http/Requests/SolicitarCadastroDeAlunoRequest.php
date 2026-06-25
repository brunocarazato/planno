<?php

namespace App\Modules\Turmas\Http\Requests;

use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SolicitarCadastroDeAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'turma_id' => ['required', 'integer', 'exists:turmas,id'],
            'nome' => ['required', 'string', 'max:120'],
            'ra' => ['required', 'string', 'max:40'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $turma = Turma::find($this->integer('turma_id'));

                if (! $turma?->aceitaCadastroDeAluno()) {
                    $validator->errors()->add('turma_id', 'Esta turma nao esta recebendo novos cadastros.');

                    return;
                }

                $ra = mb_strtoupper((string) $this->input('ra'));

                $cadastroAtivoOuPendente = CadastroAluno::query()
                    ->where('ra', $ra)
                    ->whereIn('status', [
                        CadastroAluno::STATUS_PENDENTE,
                        CadastroAluno::STATUS_APROVADO,
                    ])
                    ->exists();

                if ($cadastroAtivoOuPendente) {
                    $validator->errors()->add('ra', 'Ja existe um cadastro pendente ou aprovado para este RA.');
                }
            },
        ];
    }
}
