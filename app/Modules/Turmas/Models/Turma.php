<?php

namespace App\Modules\Turmas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    protected $table = 'turmas';

    protected $fillable = [
        'nome',
        'codigo',
        'periodo',
        'descricao',
        'aceita_novos_cadastros',
        'arquivada_em',
    ];

    protected function casts(): array
    {
        return [
            'aceita_novos_cadastros' => 'boolean',
            'arquivada_em' => 'datetime',
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
}
