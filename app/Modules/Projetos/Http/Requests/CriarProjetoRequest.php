<?php

namespace App\Modules\Projetos\Http\Requests;

use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CriarProjetoRequest extends FormRequest
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
            'turma_id' => ['required', 'integer', 'exists:turmas,id'],
            'nome' => ['required', 'string', 'max:150'],
            'codigo' => ['required', 'string', 'max:60', Rule::unique('projetos', 'codigo')],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $turma = Turma::find($this->integer('turma_id'));

                if ($turma?->estaArquivada()) {
                    $validator->errors()->add('turma_id', 'Turmas arquivadas nao aceitam novos projetos.');
                }

                if ($this->user()?->aluno()
                    && ! $this->alunoPodeUsarTurma($this->integer('turma_id'))
                ) {
                    $validator->errors()->add(
                        'turma_id',
                        'Alunos so podem criar projetos nas turmas em que possuem vinculo aprovado.',
                    );
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => mb_strtoupper((string) $this->input('codigo')),
            ]);
        }
    }

    private function alunoPodeUsarTurma(int $turmaId): bool
    {
        return CadastroAluno::query()
            ->where('user_id', $this->user()?->id)
            ->where('turma_id', $turmaId)
            ->aprovados()
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('valido_ate')
                    ->orWhereDate('valido_ate', '>=', today());
            })
            ->exists();
    }
}
