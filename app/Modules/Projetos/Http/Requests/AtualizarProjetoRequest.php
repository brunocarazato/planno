<?php

namespace App\Modules\Projetos\Http\Requests;

use App\Models\User;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'responsavel_id' => [
                'sometimes',
                Rule::prohibitedIf(fn (): bool => $this->user()?->professor() !== true),
                'required',
                'integer',
                'exists:users,id',
            ],
            'objetivo' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'justificativa' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'restricoes' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'premissas' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'entregas_esperadas' => ['sometimes', 'nullable', 'string', 'max:3000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty() || ! $this->has('responsavel_id')) {
                    return;
                }

                $projeto = $this->route('projeto');
                $responsavel = User::find($this->integer('responsavel_id'));

                if (! $projeto instanceof Projeto || ! $responsavel instanceof User) {
                    return;
                }

                if (! $this->responsavelPodeAssumirProjeto($responsavel, $projeto)) {
                    $validator->errors()->add(
                        'responsavel_id',
                        'O responsável deve ser professor ou aluno com vínculo aprovado na turma do projeto.',
                    );
                }
            },
        ];
    }

    private function responsavelPodeAssumirProjeto(User $responsavel, Projeto $projeto): bool
    {
        if ($responsavel->professor()) {
            return true;
        }

        return CadastroAluno::query()
            ->where('user_id', $responsavel->id)
            ->where('turma_id', $projeto->turma_id)
            ->aprovados()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valido_ate')
                    ->orWhereDate('valido_ate', '>=', today());
            })
            ->exists();
    }
}
