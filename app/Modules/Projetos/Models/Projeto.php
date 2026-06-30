<?php

namespace App\Modules\Projetos\Models;

use App\Models\User;
use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;
use App\Modules\GruposDeProcessos\Models\TrilhaDoProjeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projeto extends Model
{
    public const SITUACAO_EM_INICIACAO = 'em_iniciacao';

    protected $table = 'projetos';

    protected $fillable = [
        'turma_id',
        'responsavel_id',
        'nome',
        'codigo',
        'descricao',
        'situacao',
    ];

    /**
     * @return BelongsTo<Turma, Projeto>
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    /**
     * @return BelongsTo<User, Projeto>
     */
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * @return HasOne<TermoDeAbertura>
     */
    public function termoDeAbertura(): HasOne
    {
        return $this->hasOne(TermoDeAbertura::class);
    }

    /**
     * @return HasOne<TrilhaDoProjeto>
     */
    public function trilhaDosGruposDeProcessos(): HasOne
    {
        return $this->hasOne(TrilhaDoProjeto::class);
    }

    /**
     * @return HasMany<ParteInteressada>
     */
    public function partesInteressadas(): HasMany
    {
        return $this->hasMany(ParteInteressada::class);
    }

    public function situacaoFormatada(): string
    {
        return match ($this->situacao) {
            self::SITUACAO_EM_INICIACAO => 'Em iniciação',
            default => 'Situação não mapeada',
        };
    }
}
