#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

set_env() {
    key="$1"
    value="$2"

    if [ -n "$value" ]; then
        if grep -q "^${key}=" .env; then
            sed -i "s#^${key}=.*#${key}=${value}#" .env
        else
            printf '%s=%s\n' "$key" "$value" >> .env
        fi
    fi
}

set_env APP_ENV "$APP_ENV"
set_env APP_DEBUG "$APP_DEBUG"
set_env APP_URL "$APP_URL"
set_env DB_CONNECTION "$DB_CONNECTION"
set_env DB_DATABASE "$DB_DATABASE"
set_env CACHE_STORE "$CACHE_STORE"
set_env QUEUE_CONNECTION "$QUEUE_CONNECTION"
set_env SESSION_DRIVER "$SESSION_DRIVER"

if [ ! -f storage/database.sqlite ]; then
    touch storage/database.sqlite
fi

while ! mkdir storage/framework/bootstrap.lock 2>/dev/null; do
    sleep 1
done
trap 'rmdir storage/framework/bootstrap.lock 2>/dev/null || true' EXIT

if grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --force --ansi >/dev/null
fi

php artisan config:clear --ansi >/dev/null
php artisan migrate --force --ansi
rmdir storage/framework/bootstrap.lock
trap - EXIT

exec "$@"
