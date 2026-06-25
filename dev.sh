#!/usr/bin/env bash

set -euo pipefail

PROJECT_NAME="Planno"
HOST_UID="$(id -u)"
HOST_GID="$(id -g)"

dc() {
    docker compose "$@"
}

usage() {
    cat <<'USAGE'
Uso:
  ./dev.sh <comando> [argumentos]

Comandos:
  help                         Mostra esta ajuda
  up                           Sobe os containers com build
  start                        Sobe os containers sem rebuild
  down                         Para e remove os containers
  restart                      Reinicia os containers
  ps|status                    Mostra o status dos containers
  logs [servico]               Mostra logs, opcionalmente de app/nginx/mysql/node
  shell                        Abre um shell no container app
  tinker                       Abre o tinker com HOME ajustado

  migrate                      Executa migrations
  fresh                        Recria o banco local e executa migrations
  test [args]                  Executa a suite de testes PHP
  pint                         Verifica formatacao PHP
  pint-fix                     Corrige formatacao PHP
  build                        Executa o build do frontend
  routes [filtro]              Lista rotas, opcionalmente filtrando por path

  professor [RA] [senha] [nome]
                               Cria/atualiza um professor de teste.
                               Padrao: RA=PROF001 senha=senha-professor nome="Professor Teste"

Exemplos:
  ./dev.sh up
  ./dev.sh professor
  ./dev.sh professor PROF002 outra-senha "Maria Professora"
  ./dev.sh test --filter=GerenciarSessaoTest
  ./dev.sh routes turmas
USAGE
}

fix_build_owner() {
    if [[ -d public/build ]]; then
        dc run --rm node sh -c "chown -R ${HOST_UID}:${HOST_GID} public/build" >/dev/null
    fi
}

ensure_professor() {
    local ra="${1:-PROF001}"
    local senha="${2:-senha-professor}"
    local nome="${3:-Professor Teste}"
    local email

    email="$(printf '%s' "$ra" | tr '[:upper:]' '[:lower:]')@planno.local"

    local php_code
    php_code="$(cat <<'PHP'
$ra = mb_strtoupper(getenv("DEV_RA") ?: "PROF001");
$senha = getenv("DEV_PASSWORD") ?: "senha-professor";
$nome = getenv("DEV_NAME") ?: "Professor Teste";
$email = getenv("DEV_EMAIL") ?: mb_strtolower($ra)."@planno.local";

$usuario = App\Models\User::updateOrCreate(
    ["ra" => $ra],
    [
        "name" => $nome,
        "email" => $email,
        "tipo" => App\Models\User::TIPO_PROFESSOR,
        "password" => $senha,
    ],
);

echo "Professor pronto: {$usuario->name} | RA {$usuario->ra} | senha {$senha}".PHP_EOL;
PHP
)"

    dc exec \
        -e HOME=/tmp \
        -e DEV_RA="$ra" \
        -e DEV_PASSWORD="$senha" \
        -e DEV_NAME="$nome" \
        -e DEV_EMAIL="$email" \
        app php artisan tinker --execute="$php_code"
}

command="${1:-help}"
shift || true

case "$command" in
    help|-h|--help)
        usage
        ;;
    up)
        dc up -d --build
        ;;
    start)
        dc up -d
        ;;
    down)
        dc down
        ;;
    restart)
        dc restart
        ;;
    ps|status)
        dc ps
        ;;
    logs)
        dc logs -f "$@"
        ;;
    shell)
        dc exec app sh
        ;;
    tinker)
        dc exec -e HOME=/tmp app php artisan tinker
        ;;
    migrate)
        dc exec app php artisan migrate --force
        ;;
    fresh)
        dc exec app php artisan migrate:fresh --force
        ;;
    test)
        dc exec app php artisan test "$@"
        ;;
    pint)
        dc exec app ./vendor/bin/pint --test
        ;;
    pint-fix)
        dc exec app ./vendor/bin/pint
        ;;
    build)
        dc run --rm node npm run build
        fix_build_owner
        ;;
    routes)
        if [[ $# -gt 0 ]]; then
            dc exec app php artisan route:list --path="$1"
        else
            dc exec app php artisan route:list
        fi
        ;;
    professor)
        ensure_professor "$@"
        ;;
    *)
        echo "${PROJECT_NAME}: comando desconhecido: $command" >&2
        echo >&2
        usage >&2
        exit 1
        ;;
esac
