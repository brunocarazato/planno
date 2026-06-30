<?php

namespace App\Modules\GerenciamentoDeEscopo\Http\Requests;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Foundation\Http\FormRequest;

class SalvarDeclaracaoDeEscopoRequest extends FormRequest
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
            'descricao' => ['required', 'string', 'max:5000'],
            'inclui' => ['required', 'string', 'max:5000'],
            'exclusoes' => ['required', 'string', 'max:5000'],
        ];
    }
}
