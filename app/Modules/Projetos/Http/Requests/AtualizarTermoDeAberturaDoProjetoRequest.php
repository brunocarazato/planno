<?php

namespace App\Modules\Projetos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarTermoDeAberturaDoProjetoRequest extends FormRequest
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
            'objetivo' => ['nullable', 'string', 'max:3000'],
            'justificativa' => ['nullable', 'string', 'max:3000'],
            'restricoes' => ['nullable', 'string', 'max:3000'],
            'premissas' => ['nullable', 'string', 'max:3000'],
            'entregas_esperadas' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
