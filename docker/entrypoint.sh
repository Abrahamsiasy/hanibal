#!/bin/sh
set -e

cd /var/www/html

# Ensure writable runtime dirs exist (named volumes can start empty)
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# Allow key:generate --show before APP_KEY / DB exist
IS_KEY_GENERATE=false
case " $* " in
    *" key:generate "*) IS_KEY_GENERATE=true ;;
esac

if [ "$IS_KEY_GENERATE" = "false" ] && [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Generate one with:"
    echo "  docker compose run --rm --no-deps -e RUN_MIGRATIONS=false -e CACHE_CONFIG=false app php artisan key:generate --show"
    exit 1
fi

# Wait for MySQL when DB_HOST is set (skip for sqlite / key generation)
if [ "$IS_KEY_GENERATE" = "false" ] && [ "${DB_CONNECTION:-mysql}" != "sqlite" ] && [ -n "${DB_HOST:-}" ]; then
    echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
    i=0
    until php -r "
        try {
            new PDO(
                sprintf('mysql:host=%s;port=%s', getenv('DB_HOST') ?: 'mysql', getenv('DB_PORT') ?: '3306'),
                getenv('DB_USERNAME') ?: 'root',
                getenv('DB_PASSWORD') ?: ''
            );
            exit(0);
        } catch (Throwable \$e) {
            exit(1);
        }
    "; do
        i=$((i + 1))
        if [ "$i" -ge 60 ]; then
            echo "ERROR: database not reachable after 60 attempts"
            exit 1
        fi
        sleep 2
    done
    echo "Database is ready."
fi

if [ "$IS_KEY_GENERATE" = "true" ]; then
    exec "$@"
fi

php artisan storage:link --force --no-interaction || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${CACHE_CONFIG:-true}" = "true" ] && [ "${APP_ENV:-production}" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

exec "$@"
