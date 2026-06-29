<?php

namespace App\Modules\Turmas\Http\Requests;

use App\Models\User;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CadastrarAlunoPeloProfessorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->professor() === true;
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

                if (! $turma || $turma->estaArquivada()) {
                    $validator->errors()->add('turma_id', 'Selecione uma turma ativa.');

                    return;
                }

                $ra = mb_strtoupper((string) $this->input('ra'));

                if (User::query()->where('ra', $ra)->exists()) {
                    $validator->errors()->add('ra', 'Já existe uma conta de usuário para este RA.');

                    return;
                }

                if (CadastroAluno::query()->where('ra', $ra)->whereIn('status', [
                    CadastroAluno::STATUS_PENDENTE,
                    CadastroAluno::STATUS_APROVADO,
                ])->exists()) {
                    $validator->errors()->add('ra', 'Já existe um cadastro pendente ou aprovado para este RA.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ra')) {
            $this->merge([
                'ra' => mb_strtoupper((string) $this->input('ra')),
            ]);
        }
    }
}
