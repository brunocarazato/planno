<?php

namespace App\Modules\Projetos\Http\Requests;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarProjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $projeto = $this->route('projeto');

        if (! $projeto instanceof Projeto || $usuario === null) {
            return false;
        }

        return $usuario->professor()
            || ($usuario->aluno() && $projeto->responsavel_id === $usuario->id);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
