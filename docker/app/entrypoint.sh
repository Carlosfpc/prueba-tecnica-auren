#!/bin/sh
set -e

cd /var/www/html

# --- PASO 1: FIJAR PERMISOS ---
# Las carpetas ya existen, solo nos aseguramos de que www-data es el propietario.
echo ">>> Setting ownership for storage and cache..."
chown -R www-data:www-data storage bootstrap/cache

# --- PASO 2: INSTALAR DEPENDENCIAS ---
# Ahora no debería fallar.
if [ ! -d "vendor" ]; then
  echo ">>> Installing Composer dependencies..."
  composer install --no-interaction --no-progress --prefer-dist
fi

# --- PASO 3: SETUP DE LA APP ---
# (El resto del script que ya tenías)
if [ ! -f '.env' ]; then
    cp .env.example .env
fi

php artisan key:generate

echo ">>> Waiting for database..."
while ! mysqladmin ping -h"db" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent --ssl=0; do
    sleep 1
done
echo ">>> Database is ready."

php artisan migrate --force
php artisan db:seed --force
php artisan countries:sync --now
php artisan optimize:clear

echo ">>> Setup complete. Starting PHP-FPM..."
exec php-fpm