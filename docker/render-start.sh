#!/usr/bin/env bash
set -e

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    SQLITE_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "${SQLITE_DATABASE}")"
    touch "${SQLITE_DATABASE}"
fi

php artisan migrate --force

if [ "${DEMO_SEED:-true}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan storage:link --force || true

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    chown -R www-data:www-data "$(dirname "${SQLITE_DATABASE}")"
    chmod 775 "$(dirname "${SQLITE_DATABASE}")"
    chmod 664 "${SQLITE_DATABASE}"
fi

chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache
php artisan route:cache

exec apache2-foreground
