<?php

namespace App\Modules\Projetos\Http\Requests;

use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CriarProjetoRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:150'],
            'codigo' => ['required', 'string', 'max:60', Rule::unique('projetos', 'codigo')],
            'descricao' => ['nullable', 'string', 'max:1000'],
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

                if ($turma?->estaArquivada()) {
                    $validator->errors()->add('turma_id', 'Turmas arquivadas nao aceitam novos projetos.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => mb_strtoupper((string) $this->input('codigo')),
            ]);
        }
    }
}
