#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -d node_modules ]; then
    npm install
fi

exec "$@"
