<?php

namespace App\Modules\Projetos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermoDeAbertura extends Model
{
    protected $table = 'termos_de_abertura';

    protected $fillable = [
        'projeto_id',
        'objetivo',
        'justificativa',
        'restricoes',
        'premissas',
        'entregas_esperadas',
    ];

    /**
     * @return BelongsTo<Projeto, TermoDeAbertura>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }
}
