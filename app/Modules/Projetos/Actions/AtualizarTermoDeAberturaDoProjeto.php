<?php

namespace App\Modules\Projetos\Actions;

use App\Modules\Projetos\Models\Projeto;
use App\Modules\Projetos\Models\TermoDeAbertura;
use App\Modules\Projetos\Support\SanitizadorDoTermoDeAbertura;

class AtualizarTermoDeAberturaDoProjeto
{
    public function __construct(private SanitizadorDoTermoDeAbertura $sanitizador) {}

    /**
     * @param  array{objetivo?: string|null, justificativa?: string|null, restricoes?: string|null, premissas?: string|null, entregas_esperadas?: string|null}  $dados
     */
    public function executar(Projeto $projeto, array $dados): TermoDeAbertura
    {
        $termo = $projeto->termoDeAbertura()->firstOrCreate();
        $termo->update(array_map(
            fn (?string $conteudo) => $this->sanitizador->sanitizar($conteudo),
            $dados,
        ));

        return $termo;
    }
}
