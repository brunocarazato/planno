<?php

namespace App\Modules\Turmas\Http\Requests;

use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarTurmaRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:120'],
            'periodo' => ['required', 'string', Rule::in(Turma::periodos())],
            'ano' => ['required', 'integer', 'digits:4', 'min:1000', 'max:9999'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
