<?php

namespace App\Modules\GerenciamentoDasPartesInteressadas\Models;

use App\Modules\Projetos\Models\Projeto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteInteressada extends Model
{
    public const NIVEL_BAIXO = 'baixo';

    public const NIVEL_MEDIO = 'medio';

    public const NIVEL_ALTO = 'alto';

    /**
     * @var list<string>
     */
    public const NIVEIS = [
        self::NIVEL_BAIXO,
        self::NIVEL_MEDIO,
        self::NIVEL_ALTO,
    ];

    protected $table = 'partes_interessadas';

    protected $fillable = [
        'projeto_id',
        'nome',
        'papel',
        'organizacao',
        'poder',
        'interesse',
        'estrategia_engajamento',
    ];

    /**
     * @return BelongsTo<Projeto, ParteInteressada>
     */
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function poderFormatado(): string
    {
        return $this->nivelFormatado($this->poder);
    }

    public function interesseFormatado(): string
    {
        return $this->nivelFormatado($this->interesse);
    }

    private function nivelFormatado(string $nivel): string
    {
        return match ($nivel) {
            self::NIVEL_BAIXO => 'Baixo',
            self::NIVEL_MEDIO => 'Médio',
            self::NIVEL_ALTO => 'Alto',
            default => 'Não informado',
        };
    }
}
