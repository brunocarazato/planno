<?php

namespace App\Modules\Turmas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReprovarCadastroDeAlunoRequest extends FormRequest
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
            'motivo_reprovacao' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
