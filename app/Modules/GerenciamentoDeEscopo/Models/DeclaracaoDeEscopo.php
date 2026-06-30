<?php

namespace App\Modules\GerenciamentoDeEscopo\Models;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeclaracaoDeEscopo extends Model
{
    protected $table = 'declaracoes_de_escopo';

    protected $fillable = [
        'projeto_id',
        'descricao',
        'inclui',
        'exclusoes',
    ];

    /**
     * @return BelongsTo<Projeto, DeclaracaoDeEscopo>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }
}
