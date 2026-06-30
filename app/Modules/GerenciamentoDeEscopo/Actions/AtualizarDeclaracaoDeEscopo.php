<?php

namespace App\Modules\GerenciamentoDeEscopo\Actions;

use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;
use App\Modules\GerenciamentoDeEscopo\Support\SanitizadorDaDeclaracaoDeEscopo;

class AtualizarDeclaracaoDeEscopo
{
    public function __construct(private SanitizadorDaDeclaracaoDeEscopo $sanitizador) {}

    /**
     * @param  array{descricao?: string|null, inclui?: string|null, exclusoes?: string|null}  $dados
     */
    public function executar(DeclaracaoDeEscopo $declaracao, array $dados): DeclaracaoDeEscopo
    {
        $declaracao->update(array_map(
            fn (?string $conteudo) => $this->sanitizador->sanitizar($conteudo),
            $dados,
        ));

        return $declaracao;
    }
}
