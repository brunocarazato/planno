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
        $turma = $this->route('turma');

        return [
            'nome' => ['required', 'string', 'max:120'],
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('turmas', 'codigo')->ignore($turma instanceof Turma ? $turma->id : null),
            ],
            'periodo' => ['nullable', 'string', 'max:80'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
