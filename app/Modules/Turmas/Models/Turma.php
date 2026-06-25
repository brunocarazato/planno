<?php

namespace App\Modules\Turmas\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public function estaArquivada(): bool
    {
        return $this->arquivada_em !== null;
    }
}
