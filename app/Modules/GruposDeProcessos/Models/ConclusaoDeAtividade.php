<?php

namespace App\Modules\GruposDeProcessos\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConclusaoDeAtividade extends Model
{
    protected $table = 'conclusoes_atividades_grupos_processos';

    protected $fillable = [
        'chave_atividade',
        'concluida_por',
        'concluida_em',
    ];

    protected function casts(): array
    {
        return [
            'concluida_em' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<TrilhaDoProjeto, ConclusaoDeAtividade>
     */
    public function trilha(): BelongsTo
    {
        return $this->belongsTo(TrilhaDoProjeto::class, 'trilha_id');
    }

    /**
     * @return BelongsTo<User, ConclusaoDeAtividade>
     */
    public function autorDaConclusao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concluida_por');
    }
}
