<?php

namespace App\Modules\Turmas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadastroAluno extends Model
{
    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_APROVADO = 'aprovado';

    public const STATUS_REPROVADO = 'reprovado';

    public const STATUS_EXPIRADO = 'expirado';

    protected $table = 'cadastros_alunos';

    protected $fillable = [
        'turma_id',
        'nome',
        'ra',
        'status',
        'motivo_reprovacao',
        'avaliado_em',
        'valido_ate',
    ];

    protected function casts(): array
    {
        return [
            'avaliado_em' => 'datetime',
            'valido_ate' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Turma, CadastroAluno>
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopeAprovados(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APROVADO);
    }

    public function estaPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function estaAprovado(): bool
    {
        return $this->status === self::STATUS_APROVADO;
    }

    public function estaVencido(): bool
    {
        return $this->estaAprovado()
            && $this->valido_ate !== null
            && $this->valido_ate->lt(today());
    }

    public function permiteParticipacaoAtiva(): bool
    {
        return $this->estaAprovado() && ! $this->estaVencido();
    }
}
