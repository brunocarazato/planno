# Plano de Desenvolvimento do MVP

## 1. Objetivo

Construir o MVP do primeiro semestre da ferramenta pedagógica de Gestão de Projetos, priorizando a gestão inicial de turmas e a abordagem preditiva/tradicional baseada nos grupos de processos do PMBOK.

O MVP deve permitir que administradores e professores criem turmas, controlem cadastros de alunos e, depois disso, conduzam a experiência pedagógica de projetos didáticos. O aluno deve conseguir solicitar o próprio cadastro informando nome, RA e turma, e esse cadastro deve depender de aprovação e valer por 1 ano.

## 2. Escopo do MVP

Funcionalidades previstas:

```text
1. Criação de turmas
2. Permissão ou bloqueio de novos cadastros de alunos por turma
3. Cadastro autônomo de aluno com nome, RA e turma
4. Aprovação ou reprovação de cadastro de aluno
5. Validade anual do cadastro de aluno
6. Arquivamento de turma
7. Criação de projeto didático vinculado a uma turma ativa
8. Termo de abertura do projeto
9. Trilha dos grupos de processos do PMBOK
10. Cadastro de partes interessadas
11. Declaração de escopo
12. EAP/WBS simplificada
13. Cadastro de entregáveis
14. Cronograma com tarefas, marcos e dependências simples
15. Calendário próprio
16. Gantt simplificado próprio
17. Estimativa de custos básica
18. Registro de riscos
19. Solicitação de mudança
20. Análise de impacto da mudança
21. Painel de situação do projeto
22. Entregas e avaliação por rubrica
23. Encerramento e lições aprendidas
```

Fora do MVP:

- Scrum, backlog, sprints e métricas de fluxo.
- Microsserviços ou API REST pública separada.
- Componentes comerciais de calendário, scheduler, Gantt ou painel.
- Controle avançado de permissões, papéis personalizados e equipes.
- Caminho crítico completo, nivelamento de recursos e valor agregado avançado.
- Integrações externas, notificações reais e geração de documentos formais.

## 3. Premissas técnicas

- Stack principal: Laravel, Inertia.js, React, TypeScript, Tailwind CSS e shadcn/ui.
- Arquitetura: Modular Monolith com DDD pragmático.
- Banco inicial: MySQL ou PostgreSQL.
- Nomes de módulos, contextos, classes e casos de uso em português, sem acentos em identificadores técnicos.
- Termos de framework permanecem em inglês quando forem convenção técnica, como `Controller`, `Request`, `Provider`, `Repository`, `Resource`, `DTO` e `Query`.
- Componentes próprios para calendário, linha do tempo, Gantt e visualizações pedagógicas.

## 4. Módulos do MVP

Módulos fortes no MVP:

```text
Turmas
Projetos
GruposDeProcessos
GerenciamentoDeEscopo
GerenciamentoDeCronograma
GerenciamentoDeCustos
GerenciamentoDeRiscos
GerenciamentoDasPartesInteressadas
Avaliacoes
Simulacoes
```

Módulos simples no início:

```text
GerenciamentoDaQualidade
GerenciamentoDeRecursos
GerenciamentoDasComunicacoes
GerenciamentoDasAquisicoes
```

O módulo `GestaoAgil` deve ficar preparado arquiteturalmente, mas não entra no MVP do primeiro semestre.

## 5. Estratégia de entrega

O desenvolvimento deve seguir entregas verticais. Cada fase deve produzir uma parte navegável e testável da aplicação, ainda que com regras simplificadas no início.

Regra de acompanhamento: ao finalizar cada implementação relevante, atualizar os checklists deste plano marcando com `[x]` os itens concluídos.

### Fase 0 — Fundação do projeto

Objetivo: criar a base técnica para permitir evolução modular.

Entregáveis:

- [x] Projeto Laravel configurado com Inertia.js, React e TypeScript.
- [x] Tailwind CSS e shadcn/ui configurados.
- [x] Layout principal da aplicação.
- [x] Estrutura inicial de módulos em `app/Modules`.
- [x] Estrutura inicial do frontend em `resources/js/pages`, `resources/js/features` e `resources/js/shared`.
- [ ] Convenções de rotas, controllers, requests, presenters e responses definidas.
- [x] Banco de dados configurado.
- [x] Ambiente local documentado.
- [x] Ambiente Docker configurado com PHP-FPM, Nginx, MySQL e Node/Vite.

Critérios de aceite:

- [x] A aplicação abre uma tela inicial funcional.
- [x] Existe pelo menos uma rota Inertia renderizando React.
- [x] A estrutura modular está criada sem pastas desnecessárias em módulos ainda vazios.
- [x] O padrão de nomes em português está aplicado nos módulos iniciais.

Status em 25/06/2026: fundação navegável e ambiente Docker concluídos.

Validações executadas:

```text
docker compose config --quiet
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan test
docker compose exec app php artisan package:discover --ansi
docker compose exec app php artisan route:list
docker compose exec app composer validate --no-check-publish
docker compose run --rm node npm run build
curl http://localhost:8080/
```

### Fase 1 — Turmas e cadastros de alunos

Objetivo: permitir que administradores e professores gerenciem turmas e aprovem os cadastros de alunos.

Módulo principal: `Turmas`.

Entregáveis:

- [ ] Cadastro de turma.
- [ ] Edição dos dados básicos da turma.
- [ ] Permissão ou bloqueio de novos cadastros por turma.
- [ ] Arquivamento de turma.
- [ ] Tela pública ou fluxo de cadastro para aluno informar nome, RA e turma.
- [ ] Lista de cadastros pendentes por turma.
- [ ] Aprovação ou reprovação de cadastro de aluno por administrador ou professor.
- [ ] Registro da validade anual do cadastro aprovado.
- [ ] Bloqueio de uso de cadastros vencidos.

Casos de uso:

```text
CriarTurma
AtualizarTurma
PermitirNovosCadastrosNaTurma
BloquearNovosCadastrosNaTurma
ArquivarTurma
SolicitarCadastroDeAluno
AprovarCadastroDeAluno
ReprovarCadastroDeAluno
ExpirarCadastrosDeAlunoVencidos
ListarCadastrosPendentesDaTurma
```

Critérios de aceite:

- [ ] O administrador ou professor consegue criar uma turma.
- [ ] O administrador ou professor consegue permitir ou bloquear novos cadastros na turma.
- [ ] O administrador ou professor consegue arquivar uma turma.
- [ ] O aluno consegue solicitar cadastro informando nome, RA e turma.
- [ ] Um cadastro solicitado fica pendente até aprovação.
- [ ] O administrador ou professor consegue aprovar ou reprovar um cadastro.
- [ ] Um cadastro aprovado recebe validade de 1 ano.
- [ ] Cadastros vencidos não permitem participação ativa sem novo cadastro.
- [ ] Turmas arquivadas não aceitam novos cadastros nem novos projetos.

### Fase 2 — Projetos e termo de abertura

Objetivo: permitir criar e visualizar o projeto didático.

Módulo principal: `Projetos`.

Entregáveis:

- Cadastro de projeto didático.
- Associação do projeto a uma turma ativa.
- Visualização de detalhes do projeto.
- Criação e edição do termo de abertura.
- Registro de objetivo, justificativa, restrições, premissas e entregas esperadas.
- Situação inicial do projeto.

Casos de uso:

```text
CriarProjeto
AtualizarProjeto
CriarTermoDeAberturaDoProjeto
AtualizarTermoDeAberturaDoProjeto
AtualizarSituacaoIntegradaDoProjeto
```

Critérios de aceite:

- O usuário consegue criar um projeto.
- O projeto criado fica vinculado a uma turma ativa.
- O projeto possui termo de abertura vinculado.
- O sistema exibe uma visão resumida do projeto.
- O projeto criado pode seguir para a trilha dos grupos de processos.

### Fase 3 — Trilha dos grupos de processos

Objetivo: organizar a jornada pedagógica do aluno pelos grupos do PMBOK.

Módulo principal: `GruposDeProcessos`.

Entregáveis:

- Tela com os grupos: Iniciação, Planejamento, Execução, Monitoramento e Controle, Encerramento.
- Lista de atividades pedagógicas por grupo.
- Controle simples de progresso por projeto.
- Associação entre atividades e artefatos esperados.

Casos de uso:

```text
IniciarTrilhaDoProjeto
MarcarAtividadeDoGrupoComoConcluida
AtualizarProgressoDoGrupoDeProcessos
ListarArtefatosEsperadosDoProjeto
```

Critérios de aceite:

- Cada projeto possui uma trilha de grupos de processos.
- O usuário consegue marcar atividades como concluídas.
- O progresso é exibido por grupo e no resumo do projeto.

### Fase 4 — Escopo e partes interessadas

Objetivo: construir os artefatos mínimos de escopo e mapear partes interessadas.

Módulos principais:

```text
GerenciamentoDeEscopo
GerenciamentoDasPartesInteressadas
```

Entregáveis:

- Cadastro de partes interessadas.
- Registro de poder, interesse e estratégia de engajamento.
- Declaração de escopo.
- Cadastro de entregáveis.
- EAP/WBS simplificada em formato hierárquico.
- Critérios de aceitação por entregável.

Casos de uso:

```text
CadastrarParteInteressada
AtualizarParteInteressada
CriarDeclaracaoDeEscopo
AtualizarDeclaracaoDeEscopo
CriarItemDaEap
AtualizarItemDaEap
CadastrarEntregavel
DefinirCriteriosDeAceitacao
```

Critérios de aceite:

- O usuário consegue registrar partes interessadas do projeto.
- O usuário consegue criar uma declaração de escopo.
- A EAP/WBS permite pelo menos dois níveis.
- Um entregável pode estar associado a um item da EAP/WBS.

### Fase 5 — Cronograma, calendário e Gantt simplificado

Objetivo: permitir planejamento e acompanhamento básico do cronograma.

Módulo principal: `GerenciamentoDeCronograma`.

Entregáveis:

- Cadastro de atividades do cronograma.
- Marcos sem duração.
- Dependências simples entre atividades.
- Datas planejadas e realizadas.
- Linha de base simples.
- Calendário mensal/semanal próprio.
- Gantt simplificado próprio.
- Indicação visual de atraso.

Casos de uso:

```text
CriarAtividadeDoCronograma
AtualizarAtividadeDoCronograma
DefinirDependenciaEntreAtividades
RegistrarMarcoDoProjeto
CriarLinhaDeBaseDoCronograma
RegistrarAndamentoDaAtividade
CalcularAtrasoDaAtividade
```

Critérios de aceite:

- O usuário consegue criar atividades com data de início, data de fim e duração.
- O usuário consegue criar marcos.
- O sistema impede dependências inválidas simples, como uma atividade depender de si mesma.
- O calendário exibe atividades, marcos e entregas.
- O Gantt exibe barras por atividade e sinaliza atrasos.

### Fase 6 — Custos e riscos

Objetivo: conectar planejamento financeiro básico e gerenciamento de riscos ao projeto.

Módulos principais:

```text
GerenciamentoDeCustos
GerenciamentoDeRiscos
```

Entregáveis:

- Estimativa de custo por atividade ou entregável.
- Orçamento básico do projeto.
- Registro de custo real.
- Variação simples de custo.
- Cadastro de riscos.
- Probabilidade, impacto e situação do risco.
- Plano de resposta ao risco.
- Matriz de risco simples.

Casos de uso:

```text
RegistrarEstimativaDeCusto
AtualizarOrcamentoDoProjeto
RegistrarCustoReal
CalcularVariacaoDeCusto
RegistrarRisco
AtualizarRisco
DefinirRespostaAoRisco
CalcularExposicaoDoRisco
```

Critérios de aceite:

- O sistema calcula custo estimado total do projeto.
- O sistema exibe diferença entre custo estimado e custo real.
- O usuário consegue classificar riscos por probabilidade e impacto.
- O sistema destaca riscos de maior exposição.

### Fase 7 — Solicitação de mudança e análise de impacto

Objetivo: ensinar integração e controle integrado de mudanças.

Módulo principal: `Projetos/Application/UseCases/Integracao`.

Módulos consultados:

```text
GerenciamentoDeEscopo
GerenciamentoDeCronograma
GerenciamentoDeCustos
GerenciamentoDeRiscos
```

Entregáveis:

- Cadastro de solicitação de mudança.
- Justificativa e tipo da mudança.
- Análise de impacto em escopo, cronograma, custos e riscos.
- Decisão: aprovar, rejeitar ou solicitar revisão.
- Registro do histórico de decisões.
- Eventos de aplicação para módulos afetados.

Casos de uso:

```text
SubmeterSolicitacaoDeMudanca
AnalisarImpactoDaMudanca
AprovarSolicitacaoDeMudanca
RejeitarSolicitacaoDeMudanca
SolicitarRevisaoDaMudanca
AtualizarSituacaoIntegradaDoProjeto
```

Critérios de aceite:

- O usuário consegue submeter uma solicitação de mudança.
- O sistema apresenta impacto textual e numérico quando houver dados suficientes.
- Uma mudança aprovada gera registro histórico.
- Uma mudança rejeitada não altera os artefatos planejados.
- A decisão aparece no painel de situação do projeto.

### Fase 8 — Painel, avaliações, simulações e encerramento

Objetivo: consolidar a experiência pedagógica do primeiro semestre.

Módulos principais:

```text
Projetos
Avaliacoes
Simulacoes
```

Entregáveis:

- Painel de situação do projeto.
- Indicadores simples de escopo, cronograma, custos e riscos.
- Cadastro de rubricas.
- Registro de entrega avaliada.
- Feedback/devolutiva.
- Cenários simples de simulação.
- Eventos pedagógicos com impacto no projeto.
- Encerramento do projeto.
- Registro de lições aprendidas.

Casos de uso:

```text
GerarPainelDeSituacaoDoProjeto
CriarRubrica
AvaliarEntrega
RegistrarDevolutiva
CriarCenarioDeSimulacao
AplicarEventoDeSimulacao
RegistrarImpactoDaSimulacao
EncerrarProjeto
RegistrarLicoesAprendidas
```

Critérios de aceite:

- O painel resume andamento, atrasos, custos, riscos e progresso pedagógico.
- Uma entrega pode ser avaliada por rubrica.
- Uma simulação registra evento, decisão e impacto.
- O projeto pode ser encerrado com lições aprendidas.

### Fase 9 — Estabilização do MVP

Objetivo: preparar o MVP para uso em sala de aula.

Entregáveis:

- Revisão de fluxo ponta a ponta.
- Ajustes de UX nos principais formulários.
- Estados vazios, loading e erros.
- Testes dos casos de uso críticos.
- Seeders com projeto didático de exemplo.
- Documentação de instalação e execução local.
- Checklist de demonstração para professor/aluno.

Critérios de aceite:

- O fluxo de turma, solicitação de cadastro e aprovação de aluno funciona de ponta a ponta.
- Um projeto consegue passar de Iniciação a Encerramento.
- O fluxo de solicitação de mudança funciona com análise de impacto.
- Calendário e Gantt exibem dados reais do projeto.
- O painel final mostra informações consolidadas.
- O MVP pode ser demonstrado sem intervenção manual no banco.

## 6. Backlog por épicos

### Épico 1 — Fundação técnica

- [x] Criar projeto Laravel com Inertia, React e TypeScript.
- [x] Configurar Tailwind CSS e shadcn/ui.
- [x] Definir layout principal.
- [x] Criar estrutura inicial de módulos.
- [ ] Definir padrão de rotas e controllers.
- [x] Configurar banco e migrations iniciais.
- [x] Containerizar ambiente de desenvolvimento com Docker.

### Épico 2 — Turmas e cadastros de alunos

- [ ] Criar turma.
- [ ] Editar turma.
- [ ] Permitir novos cadastros na turma.
- [ ] Bloquear novos cadastros na turma.
- [ ] Arquivar turma.
- [ ] Criar fluxo de solicitação de cadastro pelo aluno.
- [ ] Registrar nome, RA e turma no cadastro do aluno.
- [ ] Listar cadastros pendentes.
- [ ] Aprovar cadastro de aluno.
- [ ] Reprovar cadastro de aluno.
- [ ] Registrar validade anual do cadastro aprovado.
- [ ] Bloquear cadastros vencidos.

### Épico 3 — Projeto didático

- [ ] Criar projeto.
- [ ] Editar projeto.
- [ ] Vincular projeto a uma turma ativa.
- [ ] Criar termo de abertura.
- [ ] Atualizar situação integrada.
- [ ] Exibir resumo do projeto.

### Épico 4 — Jornada PMBOK

- [ ] Listar grupos de processos.
- [ ] Exibir atividades pedagógicas por grupo.
- [ ] Controlar progresso por projeto.
- [ ] Relacionar artefatos aos grupos de processos.

### Épico 5 — Escopo

- [ ] Criar declaração de escopo.
- [ ] Cadastrar entregáveis.
- [ ] Criar EAP/WBS simplificada.
- [ ] Vincular entregáveis a itens da EAP/WBS.
- [ ] Definir critérios de aceitação.

### Épico 6 — Partes interessadas

- [ ] Cadastrar parte interessada.
- [ ] Registrar poder e interesse.
- [ ] Registrar estratégia de engajamento.
- [ ] Exibir matriz simples de poder/interesse.

### Épico 7 — Cronograma

- [ ] Criar atividades.
- [ ] Criar marcos.
- [ ] Definir dependências.
- [ ] Registrar linha de base.
- [ ] Registrar realizado.
- [ ] Calcular atraso.
- [ ] Exibir calendário próprio.
- [ ] Exibir Gantt simplificado.

### Épico 8 — Custos

- [ ] Registrar estimativas.
- [ ] Calcular orçamento básico.
- [ ] Registrar custos reais.
- [ ] Calcular variação de custo.

### Épico 9 — Riscos

- [ ] Registrar riscos.
- [ ] Definir probabilidade e impacto.
- [ ] Definir plano de resposta.
- [ ] Exibir matriz de risco.
- [ ] Destacar riscos materializados ou críticos.

### Épico 10 — Mudanças e integração

- [ ] Submeter solicitação de mudança.
- [ ] Analisar impacto em escopo.
- [ ] Analisar impacto em cronograma.
- [ ] Analisar impacto em custos.
- [ ] Analisar impacto em riscos.
- [ ] Aprovar solicitação.
- [ ] Rejeitar solicitação.
- [ ] Registrar histórico da decisão.

### Épico 11 — Avaliação pedagógica

- [ ] Criar rubricas.
- [ ] Cadastrar critérios.
- [ ] Registrar submissão.
- [ ] Avaliar entrega.
- [ ] Registrar devolutiva.

### Épico 12 — Simulações

- [ ] Criar cenário.
- [ ] Criar evento de simulação.
- [ ] Aplicar evento ao projeto.
- [ ] Registrar decisão do aluno.
- [ ] Registrar impacto da decisão.

### Épico 13 — Encerramento

- [ ] Encerrar projeto.
- [ ] Registrar aceite final.
- [ ] Registrar lições aprendidas.
- [ ] Gerar resumo final do projeto.

## 7. Modelo inicial de dados

Entidades mínimas por módulo:

```text
Turmas:
- Turma
- Aluno
- CadastroDeAluno
- SituacaoDoCadastroDeAluno
- AnoLetivo
- PeriodoDeValidadeDoCadastro
- PoliticaDeCadastroDaTurma

Projetos:
- Projeto
- TurmaDoProjeto
- TermoDeAberturaDoProjeto
- SolicitacaoDeMudanca
- DecisaoDeMudanca
- LicoesAprendidas

GruposDeProcessos:
- GrupoDeProcessos
- AtividadeDoGrupoDeProcessos
- ProgressoDoGrupoDeProcessos

GerenciamentoDeEscopo:
- DeclaracaoDeEscopo
- Entregavel
- ItemDaEap
- CriterioDeAceitacao

GerenciamentoDeCronograma:
- Cronograma
- AtividadeDoCronograma
- Marco
- Dependencia
- LinhaDeBase

GerenciamentoDeCustos:
- EstimativaDeCusto
- Orcamento
- CustoReal

GerenciamentoDeRiscos:
- Risco
- RespostaAoRisco
- MatrizDeRisco

GerenciamentoDasPartesInteressadas:
- ParteInteressada
- EstrategiaDeEngajamento

Avaliacoes:
- Rubrica
- Criterio
- Submissao
- Devolutiva
- Nota

Simulacoes:
- Cenario
- EventoDeSimulacao
- Decisao
- Impacto
```

## 8. Prioridade sugerida de implementação

Sequência recomendada:

```text
1. Fundação técnica
2. Turmas
3. Projetos
4. GruposDeProcessos
5. GerenciamentoDasPartesInteressadas
6. GerenciamentoDeEscopo
7. GerenciamentoDeCronograma
8. GerenciamentoDeCustos
9. GerenciamentoDeRiscos
10. Integracao de mudanças
11. Avaliacoes
12. Simulacoes
13. Encerramento e estabilização
```

Motivo da ordem:

- `Turmas` é a entrada administrativa e pedagógica da aplicação.
- `Projetos` é a raiz da experiência.
- `GruposDeProcessos` organiza a jornada pedagógica.
- Partes interessadas e escopo vêm antes do cronograma.
- Cronograma viabiliza calendário e Gantt.
- Custos e riscos tornam a análise de impacto mais significativa.
- Integração de mudanças depende dos módulos anteriores.
- Avaliações, simulações e encerramento consolidam a experiência.

## 9. Critérios gerais de pronto

Uma funcionalidade só deve ser considerada pronta quando:

- Possuir caso de uso claro na camada `Application`.
- Persistir dados necessários sem acoplamento indevido ao controller.
- Ter validação de entrada.
- Ter tela ou fluxo navegável via Inertia/React.
- Ter estados de vazio, erro e carregamento quando aplicável.
- Respeitar os nomes em português definidos na especificação.
- Não introduzir dependências pagas.
- Ter teste automatizado nos casos de uso com regra de negócio relevante.

## 10. Riscos do desenvolvimento

| Risco | Impacto | Mitigação |
| --- | --- | --- |
| Cadastro de aluno sem aprovação clara | Acesso indevido a turmas | Manter fluxo pendente/aprovado/reprovado desde o início |
| Validade anual do cadastro ficar ambígua | Alunos repetentes ou de anos anteriores podem permanecer ativos indevidamente | Registrar data de aprovação e data de validade no cadastro |
| Gantt e calendário crescerem demais | Atraso no MVP | Começar com visualização simples e poucos recursos |
| DDD excessivo em módulos simples | Complexidade desnecessária | Aplicar DDD forte apenas onde houver regra rica |
| Solicitação de mudança depender de módulos incompletos | Fluxo pedagógico fraco | Entregar análise de impacto incremental |
| Misturar cronograma preditivo com fluxo ágil | Confusão conceitual | Manter `GestaoAgil` fora do MVP do primeiro semestre |
| Avaliação pedagógica ficar genérica demais | Baixo valor didático | Vincular rubricas aos artefatos gerados pelos alunos |

## 11. Marco de conclusão do MVP

O MVP estará concluído quando for possível demonstrar o seguinte fluxo completo:

```text
1. Criar uma turma.
2. Permitir novos cadastros na turma.
3. Solicitar cadastro de aluno com nome, RA e turma.
4. Aprovar ou reprovar o cadastro do aluno.
5. Confirmar validade anual do cadastro aprovado.
6. Criar um projeto didático vinculado a uma turma ativa.
7. Criar o termo de abertura.
8. Percorrer a trilha dos grupos de processos.
9. Registrar partes interessadas.
10. Definir escopo, entregáveis e EAP/WBS.
11. Criar cronograma com atividades, marcos e dependências.
12. Visualizar calendário e Gantt.
13. Registrar custos e riscos.
14. Submeter uma solicitação de mudança.
15. Analisar impacto da mudança.
16. Aprovar ou rejeitar a mudança.
17. Visualizar painel de situação.
18. Avaliar uma entrega por rubrica.
19. Aplicar uma simulação simples.
20. Encerrar o projeto com lições aprendidas.
```
