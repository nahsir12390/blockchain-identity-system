#!/usr/bin/env bash
set -e

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p "$(dirname "${DB_DATABASE:-/var/www/html/database/database.sqlite}")"
    touch "${DB_DATABASE:-/var/www/html/database/database.sqlite}"
fi

php artisan migrate --force

if [ "${DEMO_SEED:-true}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan storage:link --force || true
php artisan config:cache
php artisan route:cache

exec apache2-foreground
