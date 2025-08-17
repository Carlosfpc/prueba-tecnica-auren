#!/bin/sh
set -e

cd /var/www/html

# Install dependencies if they are not present
if [ ! -d "vendor" ]; then
  echo ">>> Installing Composer dependencies..."
  composer install --no-interaction --no-progress --prefer-dist
fi


if [ -z "$(grep -E '^APP_KEY=' .env | cut -d '=' -f2-)" ]; then
    echo ">>> Generating application key..."
    php artisan key:generate
fi

echo ">>> Waiting for database to be ready..."
while ! mysqladmin ping -h"db" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent --ssl=0; do
    echo ">>> Waiting for database connection..."
    sleep 1
done
echo ">>> Database is ready."

echo ">>> Running database migrations..."
php artisan migrate --force

echo ">>> Seeding the database with initial data..."
php artisan db:seed --force

echo ">>> Running initial (synchronous) country synchronization..."
php artisan countries:sync --now

echo ">>> Clearing caches for a fresh start..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo ">>> Setup complete. Starting PHP-FPM..."
exec php-fpm