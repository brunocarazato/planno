# Especificação Arquitetural — Ferramenta Pedagógica de Gestão de Projetos

## 1. Visão geral do projeto

A ideia do projeto é construir uma ferramenta pedagógica completa para apoiar o ensino de Gestão de Projetos ao longo de dois semestres letivos.

No primeiro semestre, a ferramenta deve apoiar a aplicação prática dos grupos de processos do PMBOK, considerando uma abordagem mais preditiva/tradicional. No segundo semestre, a ferramenta deve evoluir para apoiar Scrum, práticas ágeis, backlog, sprints e métricas de fluxo.

A ferramenta não deve ser apenas um CRUD de projetos. Ela deve funcionar como uma plataforma didática, capaz de demonstrar visualmente impactos de decisões, atrasos, dependências, mudanças de escopo, riscos, custos e evolução do trabalho.

Antes da criação dos projetos didáticos, a ferramenta deve permitir que administradores e professores gerenciem turmas e aprovem os cadastros de alunos vinculados a elas.

---

## Ambiente local

### Com Docker

O caminho recomendado para desenvolvimento é subir a aplicação com Docker. A imagem PHP já inclui as extensões necessárias para Laravel, PHPUnit e Composer, incluindo `dom`, `xml` e `xmlwriter`.

Primeira subida:

```bash
cp .env.docker.example .env
docker compose up -d --build
docker compose exec app php artisan migrate
```

A aplicação fica disponível em:

```text
http://localhost:8080
```

Comandos úteis:

```bash
docker compose exec app php artisan test
docker compose exec app php artisan route:list
docker compose exec app composer validate --no-check-publish
docker compose run --rm node npm run build
docker compose down
```

Atalhos locais:

```bash
./dev.sh help
./dev.sh up
./dev.sh migrate
./dev.sh test
./dev.sh build
./dev.sh professor
```

O comando `./dev.sh professor` cria ou atualiza o professor de teste padrao:

```text
RA: PROF001
Senha: senha-professor
```

Serviços expostos:

```text
Aplicação: http://localhost:8080
Vite: http://localhost:5173
MySQL: 127.0.0.1:3307
```

### Sem Docker

Requisitos iniciais:

```text
PHP 8.2+
Extensao PHP DOM/XML habilitada
Composer
Node.js 20+
MySQL ou PostgreSQL
```

Primeira instalacao:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
php artisan serve
```

Para desenvolvimento do frontend:

```bash
npm run dev
```

Observacao: comandos Composer que executam `php artisan package:discover --ansi` dependem da classe `DOMDocument`. Em Ubuntu/Debian com PHP 8.3, instale/habilite `php8.3-xml` antes de rodar o fluxo completo do Composer.

---

## 2. Objetivo pedagógico por semestre

### 2.1 Primeiro semestre — PMBOK

O objetivo principal no primeiro semestre é permitir que os alunos apliquem, de forma prática, os conceitos relacionados aos grupos de processos do PMBOK:

```text
1. Iniciação
2. Planejamento
3. Execução
4. Monitoramento e Controle
5. Encerramento
```

A ferramenta deve permitir que os alunos criem e evoluam um projeto didático passando por esses grupos de processos, produzindo artefatos, tomando decisões e observando impactos em escopo, cronograma, custos, riscos, partes interessadas, comunicação, recursos e qualidade.

### 2.2 Segundo semestre — Scrum

No segundo semestre, o foco será Scrum e práticas ágeis. Por isso, a arquitetura deve manter o módulo de fluxo ágil separado dos módulos mais preditivos, permitindo que o aluno compare as abordagens sem misturar conceitos.

```text
Fase 1 — Primeiro semestre
Gestão de Projetos baseada nos grupos de processos e áreas de conhecimento do PMBOK.

Fase 2 — Segundo semestre
Scrum, práticas ágeis, backlog, sprints, eventos, papéis e métricas de fluxo.
```

---

## 3. Stack tecnológica recomendada

```text
Backend:
Laravel
Inertia.js
PHP 8.x
MySQL ou PostgreSQL

Frontend:
React
TypeScript
Tailwind CSS
shadcn/ui

Componentes e bibliotecas auxiliares:
date-fns
Dnd-kit
TanStack Table
TanStack Virtual
React Flow
Recharts ou Apache ECharts
```

A combinação principal recomendada é:

```text
Laravel + Inertia.js + React + TypeScript
```

Essa stack permite construir uma aplicação com experiência de SPA, mantendo a produtividade do Laravel e evitando a necessidade inicial de uma API REST separada.

---

## 4. Restrições importantes

Uma restrição importante do projeto é evitar custos com bibliotecas pagas.

Por isso, recomenda-se evitar dependência de componentes comerciais, especialmente para:

- calendário;
- Gantt;
- scheduler;
- painéis fechados;
- bibliotecas premium de UI.

A proposta é construir componentes próprios para:

- calendário;
- linha do tempo;
- Gantt;
- Kanban;
- simulações visuais.

Bibliotecas gratuitas e open-source podem ser usadas para resolver problemas específicos, como manipulação de datas, drag and drop, tabelas, gráficos e diagramas.

Os nomes de contextos, módulos, classes, casos de uso e conceitos de domínio devem ser definidos em português, sem acentos nos identificadores técnicos. Exceções são termos consolidados pelo framework ou pelo ecossistema, como `Controller`, `Request`, `Provider`, `Repository`, `Resource`, `DTO`, `Query`, `React`, `Inertia` e `Eloquent`.

---

## 5. Decisão sobre calendário próprio

A recomendação é não usar FullCalendar e construir um calendário próprio, mais alinhado às necessidades pedagógicas da ferramenta.

O objetivo não é criar um clone do Google Calendar, mas sim um componente de cronograma educacional, capaz de representar:

- tarefas;
- marcos;
- entregas;
- aulas;
- revisões;
- dependências;
- linha de base versus realizado;
- impactos de atraso.

As principais visualizações próprias sugeridas são:

```text
1. Calendário mensal/semanal
2. Linha do tempo
3. Gantt
4. Quadro Kanban
```

Essas visualizações devem usar os mesmos dados de domínio sempre que possível.


---

## 6. Arquitetura recomendada

A arquitetura recomendada é:

```text
Modular Monolith com DDD pragmático
```

Isso significa que a aplicação será uma única aplicação Laravel, com um único deploy e banco principal, mas organizada internamente por módulos e bounded contexts.

Não se recomenda começar com microsserviços.

---

## 7. Decisão arquitetural sobre PMBOK, áreas do conhecimento e Integração

### 7.1 Áreas do conhecimento como base dos módulos

Como o objetivo principal do primeiro semestre é trabalhar os grupos de processos do PMBOK, a arquitetura deve refletir a lógica pedagógica das áreas de conhecimento.

Assim, boa parte dos módulos do sistema será inspirada nas áreas de conhecimento do PMBOK, como por exemplo:

```text
Escopo
Cronograma
Custos
Recursos
Riscos
Partes Interessadas
```

Essas áreas ajudam a organizar a experiência do aluno e, em vários casos, também funcionam bem como bounded contexts.

A regra adotada será:

```text
As áreas do conhecimento organizam a experiência pedagógica.
Os bounded contexts organizam as regras de negócio.
```

Na maior parte dos casos, esses dois critérios coincidem. Porém, não é obrigatório que toda área de conhecimento vire um bounded context complexo desde o início.

### 7.2 Decisão sobre Gerenciamento da Integração

A área de conhecimento de Integração do PMBOK não será modelada como um bounded context próprio.

A Integração será tratada como um conjunto de casos de uso transversais dentro do módulo `Projetos`, especialmente na camada `Application`.

Essa decisão reflete a natureza integradora dessa área, que coordena informações e impactos de Escopo, Cronograma, Custos, Riscos, Partes Interessadas e demais áreas, sem concentrar as regras específicas dessas áreas.

Em vez de existir um módulo `GerenciamentoDaIntegracao`, a estrutura será:

```text
Projetos/Application/UseCases/Integracao/
 ├── CriarTermoDeAberturaDoProjeto.php
 ├── GerarPlanoDeGerenciamentoDoProjeto.php
 ├── SubmeterSolicitacaoDeMudanca.php
 ├── AnalisarImpactoDaMudanca.php
 ├── AprovarSolicitacaoDeMudanca.php
 ├── RejeitarSolicitacaoDeMudanca.php
 ├── AtualizarSituacaoIntegradaDoProjeto.php
 └── EncerrarProjeto.php
```

Ou seja, a Integração continua existindo pedagogicamente, mas arquiteturalmente atua como orquestração dentro de `Projetos`.

Exemplo: aprovação de uma solicitação de mudança.

```text
AprovarSolicitacaoDeMudanca
 ├── lê a solicitação de mudança em Projetos
 ├── consulta impacto em GerenciamentoDeEscopo
 ├── consulta impacto em GerenciamentoDeCronograma
 ├── consulta impacto em GerenciamentoDeCustos
 ├── consulta impacto em GerenciamentoDeRiscos
 ├── decide aprovação/rejeição
 ├── registra a decisão no projeto
 └── dispara eventos para os contextos afetados
```

`Projetos` não absorve as regras de Escopo, Cronograma, Custos e Riscos. Ele apenas orquestra.

---

## 8. Estrutura principal do backend

Estrutura recomendada:

```text
app/
 ├── Modules/
 │   ├── Turmas/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── Projetos/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   │   └── UseCases/
 │   │   │       └── Integracao/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GruposDeProcessos/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDeEscopo/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDeCronograma/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDeCustos/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDaQualidade/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDeRecursos/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDasComunicacoes/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDeRiscos/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDasAquisicoes/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── GerenciamentoDasPartesInteressadas/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── Avaliacoes/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   ├── Simulacoes/
 │   │   ├── Domain/
 │   │   ├── Application/
 │   │   ├── Infrastructure/
 │   │   └── Interfaces/
 │   │
 │   └── GestaoAgil/
 │       ├── Domain/
 │       ├── Application/
 │       ├── Infrastructure/
 │       └── Interfaces/
 │
 └── Shared/
     ├── Domain/
     ├── Application/
     └── Infrastructure/
```

Observação importante: não é necessário criar todas essas pastas vazias em todos os módulos desde o início. A recomendação é aplicar DDD com mais força nos módulos com maior regra de negócio e manter uma abordagem mais simples nos módulos predominantemente CRUD.

---

## 9. Camadas internas dos módulos

Cada módulo pode seguir a estrutura abaixo, conforme a complexidade:

```text
Domain/
 ├── Entities/
 ├── ValueObjects/
 ├── Services/
 ├── Events/
 ├── Exceptions/
 └── Repositories/

Application/
 ├── UseCases/
 ├── DTOs/
 └── Queries/

Infrastructure/
 ├── Persistence/
 ├── Providers/
 └── ExternalServices/

Interfaces/
 ├── Http/
 └── Presenters/
```

### 9.1 Domain

Camada responsável pelas regras centrais do negócio.

Não deve depender de Laravel, Inertia, HTTP, Eloquent, banco de dados ou React.

### 9.2 Application

Camada responsável pelos casos de uso.

Exemplos:

```text
CriarProjeto
CriarTermoDeAberturaDoProjeto
SubmeterSolicitacaoDeMudanca
AprovarSolicitacaoDeMudanca
CriarEAP
CriarAtividadeDeCronograma
MoverAtividadeDeCronograma
RegistrarRisco
```

A camada de Application coordena o fluxo, mas não deve concentrar as regras específicas dos domínios.

### 9.3 Infrastructure

Camada responsável por detalhes técnicos:

- Eloquent;
- banco de dados;
- migrations;
- repositórios concretos;
- mappers;
- providers;
- integrações externas;
- filas;
- cache.

### 9.4 Interfaces

Camada responsável pela entrada e saída com o mundo externo:

- Controllers;
- Requests;
- Presenters;
- Resources;
- Inertia render;
- API, se existir futuramente.

---

## 10. Bounded contexts e módulos sugeridos

### 10.1 Turmas

Responsável pela gestão de turmas e pelo fluxo de cadastro de alunos vinculado a cada turma.

Esse deve ser o primeiro módulo desenvolvido, pois ele organiza o acesso inicial dos alunos à ferramenta e define em qual turma cada projeto didático será criado.

Possíveis entidades e objetos:

```text
Turma
Aluno
CadastroDeAluno
SituacaoDoCadastroDeAluno
AnoLetivo
PeriodoDeValidadeDoCadastro
PoliticaDeCadastroDaTurma
```

Regras possíveis:

- administradores e professores podem criar turmas;
- administradores e professores podem permitir ou bloquear novos cadastros em uma turma;
- administradores e professores podem arquivar uma turma;
- alunos podem solicitar o próprio cadastro informando nome, RA e turma;
- um cadastro de aluno inicia como pendente;
- administradores e professores podem aprovar ou reprovar cadastros de alunos;
- uma turma arquivada não deve aceitar novos cadastros nem novos projetos;
- um cadastro aprovado deve valer por 1 ano;
- após o vencimento do cadastro, o aluno deve realizar novo cadastro para participar de uma turma no ano seguinte.

Exemplos de casos de uso:

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

### 10.2 Projetos

Responsável pelo projeto didático em si e pela visão integrada do projeto.

Cada projeto didático deve estar associado a uma turma ativa.

Possíveis entidades:

```text
Projeto
TurmaDoProjeto
ObjetivoDoProjeto
TermoDeAberturaDoProjeto
PlanoDeGerenciamentoDoProjeto
SolicitacaoDeMudanca
SituacaoDoProjeto
EncerramentoDoProjeto
LicoesAprendidas
```

Responsabilidades relacionadas à Integração:

```text
Termo de abertura
Plano de gerenciamento do projeto
Solicitações de mudança
Controle integrado de mudanças
Situação consolidada do projeto
Encerramento do projeto
Lições aprendidas
```

Exemplos de casos de uso:

```text
CriarProjeto
CriarTermoDeAberturaDoProjeto
GerarPlanoDeGerenciamentoDoProjeto
SubmeterSolicitacaoDeMudanca
AnalisarImpactoDaMudanca
AprovarSolicitacaoDeMudanca
RejeitarSolicitacaoDeMudanca
AtualizarSituacaoIntegradaDoProjeto
EncerrarProjeto
RegistrarLicoesAprendidas
```

### 10.3 GruposDeProcessos

Responsável por organizar a jornada pedagógica baseada nos grupos de processos do PMBOK durante o primeiro semestre.

Possíveis entidades e objetos:

```text
GrupoDeProcessos
AtividadeDoGrupoDeProcessos
ArtefatoDoProjeto
ModeloDeArtefato
EtapaDeAprendizagem
ProgressoDoGrupoDeProcessos
```

Grupos contemplados:

```text
Iniciacao
Planejamento
Execucao
MonitoramentoEControle
Encerramento
```

Exemplos de atividades por grupo:

```text
Iniciação:
- criar termo de abertura simplificado;
- identificar partes interessadas;
- definir objetivo e justificativa do projeto.

Planejamento:
- definir escopo;
- criar EAP/WBS;
- montar cronograma;
- identificar riscos;
- estimar custos;
- definir plano de comunicação;
- criar linha de base inicial.

Execução:
- registrar andamento das tarefas;
- produzir entregáveis;
- simular alocação de equipe;
- executar decisões do plano.

Monitoramento e Controle:
- comparar planejado versus realizado;
- acompanhar atrasos;
- controlar mudanças;
- atualizar riscos;
- analisar indicadores e desvios.

Encerramento:
- registrar lições aprendidas;
- formalizar aceite da entrega;
- avaliar desempenho da equipe;
- gerar relatório final do projeto.
```

Esse módulo funciona como uma camada de orientação pedagógica. Ele pode se comunicar com `Projetos`, `GerenciamentoDeEscopo`, `GerenciamentoDeCronograma`, `GerenciamentoDeCustos`, `GerenciamentoDeRiscos` e `Avaliacoes`, mas não deve concentrar todas as regras desses módulos.

### 10.4 GerenciamentoDeEscopo

Responsável pelo gerenciamento do escopo.

Possíveis entidades e objetos:

```text
Requisito
Entregavel
Eap
ItemDaEap
DeclaracaoDeEscopo
CriterioDeAceitacao
ValidacaoDeEscopo
```

Regras possíveis:

- um entregável pode estar associado a um item da EAP/WBS;
- uma mudança de escopo pode gerar impacto em cronograma e custos;
- um entregável pode ser validado ou rejeitado;
- critérios de aceitação devem estar associados a entregáveis.

### 10.5 GerenciamentoDeCronograma

Responsável pelo gerenciamento do cronograma.

Possíveis entidades e objetos:

```text
Cronograma
AtividadeDoCronograma
Marco
Dependencia
LinhaDeBase
IntervaloDeDatas
Duracao
CalendarioDeTrabalho
CaminhoCritico
```

Regras possíveis:

- uma atividade possui duração;
- uma atividade pode depender de outra;
- um marco não possui duração;
- uma alteração de data pode impactar atividades dependentes;
- o cronograma pode possuir uma linha de base;
- o sistema pode calcular atraso, folga e caminho crítico.

Esse é um dos contextos mais ricos do sistema e deve receber uma modelagem mais cuidadosa.

### 10.6 GerenciamentoDeCustos

Responsável pelo gerenciamento de custos.

Possíveis entidades e objetos:

```text
EstimativaDeCusto
Orcamento
LinhaDeBaseDeCustos
CustoReal
VariacaoDeCusto
MetricaDeValorAgregado
```

Regras possíveis:

- uma atividade pode possuir custo estimado;
- um projeto pode possuir orçamento aprovado;
- custos reais podem ser registrados ao longo da execução;
- o sistema pode calcular variação de custo e indicadores simples de valor agregado.

### 10.7 GerenciamentoDaQualidade

Responsável pelo gerenciamento da qualidade.

Possíveis entidades e objetos:

```text
PlanoDeQualidade
CriterioDeQualidade
MetricaDeQualidade
ChecklistDeQualidade
RegistroDeInspecao
ResultadoDeQualidade
```

Regras possíveis:

- um entregável pode possuir critérios de qualidade;
- uma revisão pode registrar conformidades e não conformidades;
- um resultado de qualidade pode gerar ajustes em escopo, cronograma ou custos.

No MVP, esse módulo pode começar simples e evoluir conforme as atividades pedagógicas exigirem maior controle de qualidade.

### 10.8 GerenciamentoDeRecursos

Responsável pelo gerenciamento de recursos.

Possíveis entidades e objetos:

```text
Recurso
MembroDaEquipe
Papel
Alocacao
Disponibilidade
CargaDeTrabalho
```

Regras possíveis:

- uma atividade pode ter um responsável e também um executor;
- a disponibilidade do executor pode impactar o cronograma;
- o sistema pode indicar sobrecarga de um membro da equipe.

### 10.9 GerenciamentoDasComunicacoes

Responsável pelo gerenciamento das comunicações.

Possíveis entidades e objetos:

```text
PlanoDeComunicacao
RegistroDeReuniao
RelatorioDeSituacao
CanalDeComunicacao
Notificacao
```

Regras possíveis:

- um projeto pode ter um plano de comunicação;
- uma reunião pode gerar ata;
- um relatório de situação pode consolidar informações de escopo, cronograma, custos e riscos.

No MVP, esse módulo pode começar como parte simples do projeto.

### 10.10 GerenciamentoDeRiscos

Responsável pelo gerenciamento de riscos.

Possíveis entidades e objetos:

```text
Risco
CategoriaDeRisco
RespostaAoRisco
Probabilidade
Impacto
MatrizDeRisco
SituacaoDoRisco
```

Regras possíveis:

- um risco possui probabilidade e impacto;
- um risco pode ter plano de resposta;
- um risco pode estar associado a uma atividade, entregável ou projeto;
- um risco materializado pode gerar impacto em cronograma, custos ou escopo.

### 10.11 GerenciamentoDasAquisicoes

Responsável pelo gerenciamento de aquisições.

Possíveis entidades e objetos:

```text
PlanoDeAquisicao
Fornecedor
Proposta
Contrato
DecisaoFazerOuComprar
CriterioDeSelecao
```

Regras possíveis:

- uma aquisição pode estar vinculada a um entregável ou necessidade do projeto;
- fornecedores fictícios podem ser avaliados em uma simulação;
- critérios de seleção podem apoiar decisões dos alunos.

No MVP, esse módulo pode ser deixado para evolução futura ou tratado como atividade simulada.

### 10.12 GerenciamentoDasPartesInteressadas

Responsável pelo gerenciamento das partes interessadas.

Possíveis entidades e objetos:

```text
ParteInteressada
RegistroDePartesInteressadas
MatrizPoderInteresse
NivelDeEngajamento
EstrategiaDeEngajamento
```

Regras possíveis:

- uma parte interessada pode ter nível de poder e interesse;
- o sistema pode comparar engajamento atual e desejado;
- uma estratégia de engajamento pode estar ligada a uma parte interessada;
- partes interessadas podem influenciar riscos, mudanças e critérios de aceitação.

### 10.13 Avaliacoes

Responsável pela avaliação pedagógica.

Possíveis entidades:

```text
AtividadeAvaliativa
Rubrica
Criterio
Submissao
Devolutiva
Nota
```

Regras possíveis:

- uma entrega pode ser avaliada por rubrica;
- uma rubrica possui critérios;
- uma devolutiva pode ser manual ou gerada por IA;
- uma submissão pertence a um aluno ou equipe.

### 10.14 Simulacoes

Responsável por cenários, eventos e decisões pedagógicas.

Possíveis entidades e objetos:

```text
Cenario
EventoDeSimulacao
Decisao
ExecucaoDeSimulacao
Impacto
ResultadoDeAprendizagem
```

Exemplos de eventos:

```text
Cliente mudou o escopo.
Um membro da equipe ficou indisponível.
A tarefa crítica atrasou.
Um risco se materializou.
O professor adicionou uma restrição.
```

Regras possíveis:

- uma decisão do aluno gera impactos;
- um impacto pode afetar prazo, escopo, custo, qualidade ou risco;
- uma simulação registra a sequência de decisões.

### 10.15 GestaoAgil

Responsável por Scrum, Kanban e métricas de fluxo.

Possíveis entidades:

```text
BacklogDoProduto
ItemDoBacklogDoProduto
Sprint
BacklogDaSprint
Quadro
Coluna
ItemDeTrabalho
LimiteDeWip
MetricaDeFluxo
```

Regras possíveis:

- um item de backlog pode entrar em uma sprint;
- uma coluna pode ter limite de WIP;
- um cartão muda de estado ao trocar de coluna;
- o tempo de ciclo pode começar quando o item entra em andamento;
- a vazão pode ser calculada por período.

Esse contexto deve ser separado de `GerenciamentoDeCronograma`, porque uma atividade no cronograma e um cartão no Kanban podem parecer similares, mas possuem significados diferentes no domínio.

---

## 11. Organização do frontend

No frontend, a organização deve seguir domínios/features, sem tentar aplicar DDD com o mesmo rigor do backend.

```text
resources/js/
 ├── app/
 │   ├── layouts/
 │   ├── providers/
 │   └── routes/
 │
 ├── pages/
 │   ├── Turmas/
 │   ├── Projetos/
 │   ├── GruposDeProcessos/
 │   ├── GerenciamentoDeEscopo/
 │   ├── GerenciamentoDeCronograma/
 │   ├── GerenciamentoDeCustos/
 │   ├── GerenciamentoDaQualidade/
 │   ├── GerenciamentoDeRecursos/
 │   ├── GerenciamentoDasComunicacoes/
 │   ├── GerenciamentoDeRiscos/
 │   ├── GerenciamentoDasAquisicoes/
 │   ├── GerenciamentoDasPartesInteressadas/
 │   ├── Avaliacoes/
 │   ├── Simulacoes/
 │   └── GestaoAgil/
 │
 ├── features/
 │   ├── turmas/
 │   ├── projetos/
 │   ├── grupos-de-processos/
 │   ├── gerenciamento-de-escopo/
 │   ├── gerenciamento-de-cronograma/
 │   │   ├── components/
 │   │   │   ├── Calendario/
 │   │   │   ├── Gantt/
 │   │   │   └── LinhaDoTempo/
 │   │   ├── hooks/
 │   │   ├── types/
 │   │   └── utils/
 │   ├── gerenciamento-de-custos/
 │   ├── gerenciamento-da-qualidade/
 │   ├── gerenciamento-de-recursos/
 │   ├── gerenciamento-das-comunicacoes/
 │   ├── gerenciamento-de-riscos/
 │   ├── gerenciamento-das-aquisicoes/
 │   ├── gerenciamento-das-partes-interessadas/
 │   ├── avaliacoes/
 │   ├── simulacoes/
 │   └── gestao-agil/
 │
 └── shared/
     ├── components/
     │   └── ui/
     ├── hooks/
     ├── lib/
     └── types/
```

---

## 12. Fluxo de exemplo — Solicitação de mudança

Um fluxo importante para ensinar Integração, Monitoramento e Controle seria a solicitação de mudança.

```text
React/Inertia
 ↓
SubmeterSolicitacaoDeMudancaController
 ↓
Projetos/Application/UseCases/Integracao/SubmeterSolicitacaoDeMudanca
 ↓
Projetos/Domain/SolicitacaoDeMudanca
 ↓
AnalisarImpactoDaMudanca
 ├── GerenciamentoDeEscopo/Application/Queries/ObterImpactoNoEscopo
 ├── GerenciamentoDeCronograma/Application/Queries/ObterImpactoNoCronograma
 ├── GerenciamentoDeCustos/Application/Queries/ObterImpactoNosCustos
 └── GerenciamentoDeRiscos/Application/Queries/ObterImpactoNosRiscos
 ↓
AprovarSolicitacaoDeMudanca ou RejeitarSolicitacaoDeMudanca
 ↓
Eventos para módulos afetados
```

Resultado pedagógico esperado para o aluno:

```text
A mudança solicitada impacta:
- Escopo: adiciona 2 novos entregáveis.
- Cronograma: aumenta o prazo em 5 dias.
- Custos: adiciona R$ 1.200 ao orçamento estimado.
- Riscos: aumenta a exposição do risco de atraso.

Decisão: aprovar, rejeitar ou solicitar revisão.
```

---

## 13. Comunicação entre contextos

A comunicação entre contextos pode ser feita com eventos de domínio ou eventos de aplicação.

Exemplos:

```text
CadastroDeAlunoAprovado
TurmaArquivada
SolicitacaoDeMudancaAprovada
AtividadeAtrasada
RiscoMaterializado
EntregavelAceito
ProjetoEncerrado
```

Exemplo de comunicação:

```text
Projetos dispara:
SolicitacaoDeMudancaAprovada

GerenciamentoDeCronograma escuta:
atualizar cronograma, se necessário

GerenciamentoDeCustos escuta:
atualizar linha de base de custos, se necessário

GerenciamentoDeRiscos escuta:
atualizar exposição de riscos, se necessário

Avaliacoes escuta:
registrar impacto pedagógico da decisão
```

No início, recomenda-se usar eventos com moderação, apenas quando houver real necessidade de desacoplamento entre contextos.

---

## 14. Context map inicial

```text
Turmas
   ↓
Projetos
   ↓
GruposDeProcessos

Turmas → Projetos
Turmas → Avaliacoes

Projetos → GerenciamentoDeEscopo
Projetos → GerenciamentoDeCronograma
Projetos → GerenciamentoDeCustos
Projetos → GerenciamentoDaQualidade
Projetos → GerenciamentoDeRecursos
Projetos → GerenciamentoDasComunicacoes
Projetos → GerenciamentoDeRiscos
Projetos → GerenciamentoDasAquisicoes
Projetos → GerenciamentoDasPartesInteressadas

Projetos/Application/UseCases/Integracao
   → orquestra impactos entre áreas de conhecimento

GruposDeProcessos → Avaliacoes
Simulacoes → GerenciamentoDeEscopo
Simulacoes → GerenciamentoDeCronograma
Simulacoes → GerenciamentoDeCustos
Simulacoes → GerenciamentoDeRiscos
GestaoAgil → Avaliacoes
```

Interpretação:

```text
Turmas organiza turmas, cadastros de alunos e validade anual desses cadastros.
Projetos representa o projeto didático e centraliza a visão integrada.
GruposDeProcessos guia a jornada pedagógica do PMBOK.
As áreas de conhecimento concentram regras específicas.
Integracao, dentro de Projetos/Application, orquestra decisões transversais.
Avaliacoes avalia o desempenho dos alunos.
Simulacoes cria cenários e impactos.
GestaoAgil entra com força no segundo semestre para Scrum.
```

---

## 15. Prioridade de implementação

### 15.1 Primeiro módulo do MVP

```text
Turmas
```

Esse módulo deve ser desenvolvido primeiro, pois habilita a criação das turmas, o controle de cadastros de alunos e a associação posterior dos projetos didáticos a uma turma ativa.

### 15.2 Módulos fortes no MVP do primeiro semestre

```text
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

### 15.3 Módulos que podem começar simples

```text
GerenciamentoDaQualidade
GerenciamentoDeRecursos
GerenciamentoDasComunicacoes
GerenciamentoDasAquisicoes
```

### 15.4 Módulo prioritário no segundo semestre

```text
GestaoAgil
```

---

## 16. MVP sugerido para o primeiro semestre

Como o objetivo inicial é cobrir os grupos de processos do PMBOK no primeiro semestre, o MVP deve priorizar a abordagem preditiva/tradicional de Gestão de Projetos.

Funcionalidades iniciais:

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

---

## 17. Evolução para o segundo semestre — Scrum

No segundo semestre, a ferramenta deve evoluir para incluir Scrum e práticas ágeis dentro do módulo `GestaoAgil`.

Funcionalidades futuras:

```text
1. Product Backlog
2. User Stories
3. Critérios de aceitação
4. Priorização
5. Sprint Planning
6. Sprint Backlog
7. Daily Scrum simulada ou registrada
8. Sprint Review
9. Sprint Retrospective
10. Métricas de fluxo
11. Burndown/Burnup
12. Comparação entre abordagem preditiva e ágil
```

Essa evolução deve acontecer principalmente dentro do contexto `GestaoAgil`, sem misturar regras de Scrum dentro de `GerenciamentoDeCronograma`.

---

## 18. Recomendação final

A recomendação final é:

```text
Laravel + Inertia + React + TypeScript
com Modular Monolith orientado a Bounded Contexts
aplicando DDD pragmático, com roadmap pedagógico por semestre
```

Usar DDD forte onde há regra rica:

```text
Turmas
Projetos/Application/UseCases/Integracao
GruposDeProcessos
GerenciamentoDeEscopo
GerenciamentoDeCronograma
GerenciamentoDeCustos
GerenciamentoDeRiscos
GerenciamentoDasPartesInteressadas
Simulacoes
Avaliacoes
GestaoAgil
```

Usar abordagem mais simples onde houver CRUD básico:

```text
GerenciamentoDaQualidade no início
GerenciamentoDeRecursos no início
GerenciamentoDasComunicacoes no início
GerenciamentoDasAquisicoes no início
```

Principais decisões registradas:

```text
1. A arquitetura será um Modular Monolith com DDD pragmático.
2. As áreas do conhecimento do PMBOK orientarão boa parte dos módulos.
3. Turmas será o primeiro módulo do MVP.
4. O cadastro de aluno será feito pelo próprio aluno e dependerá de aprovação de administrador ou professor.
5. Um cadastro aprovado de aluno terá validade de 1 ano.
6. GerenciamentoDaIntegracao não será um bounded context próprio.
7. A Integração será implementada como casos de uso transversais dentro de Projetos/Application/UseCases/Integracao.
8. O primeiro semestre prioriza PMBOK e abordagem preditiva.
9. O segundo semestre prioriza Scrum dentro de GestaoAgil.
10. Calendário, Gantt, Linha do Tempo e Kanban serão componentes próprios para evitar dependências pagas.
```

Essa combinação oferece equilíbrio entre produtividade, baixo custo, organização, manutenção, didática, evolução futura e aderência aos conceitos de DDD.
