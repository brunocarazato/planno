<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Http\Requests;

use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;
use App\Modules\Projetos\Models\Projeto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CadastrarParteInteressadaRequest extends FormRequest
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
            'papel' => ['nullable', 'string', 'max:120'],
            'organizacao' => ['nullable', 'string', 'max:150'],
            'poder' => ['required', Rule::in(ParteInteressada::NIVEIS)],
            'interesse' => ['required', Rule::in(ParteInteressada::NIVEIS)],
            'estrategia_engajamento' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
