# Convencoes Arquiteturais

Este projeto segue um monolito modular pragmático. Cada modulo de negocio vive em `app/Modules/<Modulo>` e deve crescer apenas quando houver uma entrega vertical que justifique novos arquivos.

## Backend

- Rotas web ficam em `routes/web.php`, agrupadas por prefixo e nome do modulo.
- Controllers ficam em `app/Modules/<Modulo>/Http/Controllers`.
- Requests ficam em `app/Modules/<Modulo>/Http/Requests` e concentram validacao e autorizacao inicial.
- Casos de uso ficam em `app/Modules/<Modulo>/Actions`, com metodo publico `executar`.
- Models Eloquent ficam em `app/Modules/<Modulo>/Models`.
- Presenters ficam em `app/Modules/<Modulo>/Presenters` e transformam dados para paginas Inertia.
- Responses Inertia devem ser retornadas pelo controller, usando presenters para evitar regras de apresentacao espalhadas.

## Frontend

- Paginas Inertia ficam em `resources/js/pages`.
- Funcionalidades reutilizaveis devem ir para `resources/js/features` quando surgirem dois ou mais usos reais.
- Componentes de UI compartilhados ficam em `resources/js/shared/ui`.
- Layouts compartilhados ficam em `resources/js/shared/layouts`.
- Chamadas de navegacao e formularios devem usar `@inertiajs/react`.

## Nomes

- Identificadores de dominio devem usar portugues sem acentos: `Turma`, `CriarTurma`, `ArquivarTurma`.
- Termos de framework permanecem em ingles quando forem convencao: `Controller`, `Request`, `Presenter`, `Response`, `Model`.
- Rotas publicas usam URLs em portugues sem acentos e separadas por hifen.
