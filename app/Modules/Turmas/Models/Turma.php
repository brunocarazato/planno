<?php

namespace App\Modules\Turmas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    public const PERIODO_PRIMEIRO_SEMESTRE = '1';
    public const PERIODO_SEGUNDO_SEMESTRE = '2';

    protected $table = 'turmas';

    protected $fillable = [
        'nome',
        'codigo',
        'periodo',
        'ano',
        'descricao',
        'aceita_novos_cadastros',
        'arquivada_em',
    ];

    protected function casts(): array
    {
        return [
            'ano' => 'integer',
            'aceita_novos_cadastros' => 'boolean',
            'arquivada_em' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function periodos(): array
    {
        return [
            self::PERIODO_PRIMEIRO_SEMESTRE,
            self::PERIODO_SEGUNDO_SEMESTRE,
        ];
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->whereNull('arquivada_em');
    }

    /**
     * @return HasMany<CadastroAluno>
     */
    public function cadastrosAlunos(): HasMany
    {
        return $this->hasMany(CadastroAluno::class);
    }

    public function estaArquivada(): bool
    {
        return $this->arquivada_em !== null;
    }

    public function aceitaCadastroDeAluno(): bool
    {
        return ! $this->estaArquivada() && $this->aceita_novos_cadastros;
    }

    public function periodoFormatado(): ?string
    {
        $periodo = match ($this->periodo) {
            self::PERIODO_PRIMEIRO_SEMESTRE => '1º Semestre',
            self::PERIODO_SEGUNDO_SEMESTRE => '2º Semestre',
            default => null,
        };

        if ($periodo === null) {
            return $this->ano !== null ? (string) $this->ano : null;
        }

        return $this->ano !== null ? "{$periodo} de {$this->ano}" : $periodo;
    }
}
