#!/bin/sh
set -e
cd /var/www/html

# Read the DB password from the mounted secret (falls back to env).
if [ -f /run/secrets/postgres_password ]; then
    DB_PASSWORD="$(cat /run/secrets/postgres_password)"
    export DB_PASSWORD
fi

# Ephemeral app key if none supplied (experiment only).
if [ -z "$APP_KEY" ]; then
    APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    export APP_KEY
    echo "[app] generated ephemeral APP_KEY"
fi

# Ensure writable framework directories exist on the storage volume.
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/app/public storage/logs
chown -R www-data:www-data storage bootstrap/cache

echo "[app] waiting for ${DB_WRITE_HOST:-db-primary}:${DB_PORT:-5432} ..."
until php -r '$h=getenv("DB_WRITE_HOST")?:"db-primary"; $p=getenv("DB_PORT")?:5432; exit(@fsockopen($h,(int)$p)?0:1);' 2>/dev/null; do
    sleep 2
done

php artisan package:discover --ansi || true
php artisan migrate --force

# Seed once per storage volume.
if [ ! -f storage/.seeded ]; then
    if php artisan db:seed --force; then
        touch storage/.seeded
    fi
fi

php artisan config:clear
exec "$@"
