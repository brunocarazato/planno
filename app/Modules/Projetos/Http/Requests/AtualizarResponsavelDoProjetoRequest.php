<?php

namespace App\Modules\Projetos\Http\Requests;

use App\Models\User;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AtualizarResponsavelDoProjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->professor() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'responsavel_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
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
