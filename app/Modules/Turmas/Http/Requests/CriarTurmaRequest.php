<?php

namespace App\Modules\Turmas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriarTurmaRequest extends FormRequest
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
            'codigo' => ['required', 'string', 'max:30', Rule::unique('turmas', 'codigo')],
            'periodo' => ['nullable', 'string', 'max:80'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
