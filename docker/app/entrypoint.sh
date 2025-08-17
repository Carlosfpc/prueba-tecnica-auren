#!/bin/sh
set -e

cd /var/www/html

# --- PASO 1: CORRECCIÓN DE PERMISOS ---
# Asegurarse de que el usuario www-data es propietario de todo ANTES de empezar.
echo ">>> Setting ownership of application files..."
chown -R www-data:www-data .

# --- PASO 2: EJECUTAR COMPOSER COMO WWW-DATA ---
# Si vendor no existe, instalamos como el usuario correcto.
if [ ! -d "vendor" ]; then
  echo ">>> Installing Composer dependencies as www-data user..."
  # Usamos 'su' para cambiar de usuario temporalmente
  su -s /bin/sh -c "composer install --no-interaction --no-progress --prefer-dist" www-data
fi

# --- PASO 3: EJECUTAR ARTISAN COMO WWW-DATA ---
# Agrupamos todos los comandos de artisan y los ejecutamos como www-data
echo ">>> Running setup commands as www-data user..."
su -s /bin/sh -c "
    # Copiamos el .env si no existe
    if [ ! -f '.env' ]; then
        cp .env.example .env
        php artisan key:generate
    fi
    
    # Esperamos a la base de datos
    echo '>>> Waiting for database...'
    while ! mysqladmin ping -h'db' -u'${DB_USERNAME}' -p'${DB_PASSWORD}' --silent --ssl=0; do
        sleep 1
    done
    echo '>>> Database is ready.'

    # Ejecutamos migraciones, seeders y la sync inicial
    php artisan migrate --force
    php artisan db:seed --force
    php artisan countries:sync --now
    
    # Limpiamos cachés
    php artisan optimize:clear
" www-data

echo ">>> Setup complete. Starting PHP-FPM..."
exec php-fpm