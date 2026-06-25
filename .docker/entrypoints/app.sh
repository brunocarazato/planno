#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    if [ -f .env.docker.example ]; then
        cp .env.docker.example .env
    else
        cp .env.example .env
    fi
fi

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache

if [ ! -f vendor/autoload.php ]; then
    composer install
fi

if grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --ansi
fi

exec "$@"
