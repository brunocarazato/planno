<?php

namespace App\Modules\GerenciamentoDeEscopo\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class SanitizadorDaDeclaracaoDeEscopo
{
    private HTMLPurifier $purificador;

    public function __construct()
    {
        $configuracao = HTMLPurifier_Config::createDefault();
        $configuracao->set('HTML.Allowed', 'p,strong,em,ul,ol,li,br');
        $configuracao->set('Cache.SerializerPath', storage_path('framework/cache/data'));

        $this->purificador = new HTMLPurifier($configuracao);
    }

    public function sanitizar(?string $conteudo): ?string
    {
        if ($conteudo === null) {
            return null;
        }

        return trim($this->purificador->purify($conteudo));
    }
}
