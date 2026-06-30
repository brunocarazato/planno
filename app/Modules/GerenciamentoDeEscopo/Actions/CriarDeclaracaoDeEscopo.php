<?php

namespace App\Modules\GerenciamentoDeEscopo\Actions;

use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;
use App\Modules\GerenciamentoDeEscopo\Support\SanitizadorDaDeclaracaoDeEscopo;
use App\Modules\Projetos\Models\Projeto;

class CriarDeclaracaoDeEscopo
{
    public function __construct(private SanitizadorDaDeclaracaoDeEscopo $sanitizador) {}

    /**
     * @param  array{descricao?: string|null, inclui?: string|null, exclusoes?: string|null}  $dados
     */
    public function executar(Projeto $projeto, array $dados): DeclaracaoDeEscopo
    {
        return $projeto->declaracaoDeEscopo()->create($this->sanitizar($dados));
    }

    /**
     * @param  array<string, string|null>  $dados
     * @return array<string, string|null>
     */
    private function sanitizar(array $dados): array
    {
        return array_map(
            fn (?string $conteudo) => $this->sanitizador->sanitizar($conteudo),
            $dados,
        );
    }
}
