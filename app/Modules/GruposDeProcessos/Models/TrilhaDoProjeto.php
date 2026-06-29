<?php

namespace App\Modules\GruposDeProcessos\Models;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrilhaDoProjeto extends Model
{
    protected $table = 'trilhas_grupos_processos';

    protected $fillable = ['projeto_id'];

    /**
     * @return BelongsTo<Projeto, TrilhaDoProjeto>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    /**
     * @return HasMany<ConclusaoDeAtividade>
     */
    public function conclusoes(): HasMany
    {
        return $this->hasMany(ConclusaoDeAtividade::class, 'trilha_id');
    }
}
