<?php

namespace App\Modules\GruposDeProcessos\Support;

class CatalogoDaTrilhaDosGruposDeProcessos
{
    /**
     * @return list<array{
     *     chave: string,
     *     nome: string,
     *     descricao: string,
     *     atividades: list<array{chave: string, titulo: string, descricao: string, artefato: string}>
     * }>
     */
    public function grupos(): array
    {
        return [
            [
                'chave' => 'iniciacao',
                'nome' => 'Iniciação',
                'descricao' => 'Transforme uma ideia em um projeto autorizado e compreendido.',
                'atividades' => [
                    [
                        'chave' => 'elaborar-termo-de-abertura',
                        'titulo' => 'Elaborar o termo de abertura',
                        'descricao' => 'Registre objetivo, justificativa, premissas, restrições e entregas esperadas.',
                        'artefato' => 'Termo de abertura',
                    ],
                    [
                        'chave' => 'identificar-partes-interessadas-iniciais',
                        'titulo' => 'Identificar partes interessadas iniciais',
                        'descricao' => 'Reconheça quem influencia o projeto ou é impactado por seus resultados.',
                        'artefato' => 'Registro de partes interessadas',
                    ],
                ],
            ],
            [
                'chave' => 'planejamento',
                'nome' => 'Planejamento',
                'descricao' => 'Converta a intenção em um caminho executável e verificável.',
                'atividades' => [
                    [
                        'chave' => 'definir-escopo-e-entregaveis',
                        'titulo' => 'Definir escopo e entregáveis',
                        'descricao' => 'Delimite o trabalho necessário e os resultados que serão aceitos.',
                        'artefato' => 'Declaração de escopo',
                    ],
                    [
                        'chave' => 'estruturar-eap',
                        'titulo' => 'Estruturar a EAP',
                        'descricao' => 'Decomponha as entregas em partes menores e gerenciáveis.',
                        'artefato' => 'EAP/WBS',
                    ],
                    [
                        'chave' => 'planejar-cronograma',
                        'titulo' => 'Planejar o cronograma',
                        'descricao' => 'Organize atividades, marcos, datas e dependências do projeto.',
                        'artefato' => 'Cronograma',
                    ],
                    [
                        'chave' => 'estimar-custos-e-riscos',
                        'titulo' => 'Estimar custos e riscos',
                        'descricao' => 'Antecipe recursos financeiros e eventos que podem afetar o plano.',
                        'artefato' => 'Orçamento e registro de riscos',
                    ],
                ],
            ],
            [
                'chave' => 'execucao',
                'nome' => 'Execução',
                'descricao' => 'Realize o trabalho planejado e produza as entregas do projeto.',
                'atividades' => [
                    [
                        'chave' => 'registrar-andamento-das-entregas',
                        'titulo' => 'Registrar andamento das entregas',
                        'descricao' => 'Atualize o trabalho realizado e as evidências produzidas pela equipe.',
                        'artefato' => 'Registro de execução',
                    ],
                    [
                        'chave' => 'mobilizar-partes-interessadas',
                        'titulo' => 'Mobilizar as partes interessadas',
                        'descricao' => 'Aplique as estratégias de comunicação e engajamento planejadas.',
                        'artefato' => 'Registro de engajamento',
                    ],
                ],
            ],
            [
                'chave' => 'monitoramento-e-controle',
                'nome' => 'Monitoramento e Controle',
                'descricao' => 'Compare plano e realidade para decidir correções com consciência.',
                'atividades' => [
                    [
                        'chave' => 'analisar-desempenho-integrado',
                        'titulo' => 'Analisar o desempenho integrado',
                        'descricao' => 'Observe progresso, atrasos, custos e riscos em conjunto.',
                        'artefato' => 'Painel de situação',
                    ],
                    [
                        'chave' => 'avaliar-solicitacoes-de-mudanca',
                        'titulo' => 'Avaliar solicitações de mudança',
                        'descricao' => 'Registre decisões e seus impactos antes de alterar o plano.',
                        'artefato' => 'Registro de mudanças',
                    ],
                ],
            ],
            [
                'chave' => 'encerramento',
                'nome' => 'Encerramento',
                'descricao' => 'Formalize o resultado, reflita sobre o percurso e preserve aprendizados.',
                'atividades' => [
                    [
                        'chave' => 'formalizar-aceite-das-entregas',
                        'titulo' => 'Formalizar o aceite das entregas',
                        'descricao' => 'Confirme os critérios atendidos e o resultado final do projeto.',
                        'artefato' => 'Aceite final',
                    ],
                    [
                        'chave' => 'registrar-licoes-aprendidas',
                        'titulo' => 'Registrar lições aprendidas',
                        'descricao' => 'Consolide descobertas úteis para os próximos projetos.',
                        'artefato' => 'Lições aprendidas',
                    ],
                ],
            ],
        ];
    }

    public function possuiAtividade(string $chave): bool
    {
        foreach ($this->grupos() as $grupo) {
            foreach ($grupo['atividades'] as $atividade) {
                if ($atividade['chave'] === $chave) {
                    return true;
                }
            }
        }

        return false;
    }
}
