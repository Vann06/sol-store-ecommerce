#!/bin/sh
set -e

cd /var/www/html

# Asegurar .env y APP_KEY
if [ ! -f .env ]; then
    echo "Creando .env desde .env.example..."
    cp .env.example .env || true
fi

# Instalar dependencias si falta autoload (volumen vacío)
if [ ! -f vendor/autoload.php ]; then
    echo "Instalando dependencias de Composer..."
    composer install --prefer-dist --no-interaction --optimize-autoloader
fi

# Generar APP_KEY si falta
if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force || true
fi

if ! grep -q '^JWT_SECRET=' .env || [ -z "$(grep '^JWT_SECRET=' .env | cut -d '=' -f2)" ]; then
    echo "Generando JWT_SECRET..."
    php artisan jwt:secret --force || true
fi

# Permisos para storage y cache (puede ser bind mount desde host)
echo "Ajustando permisos de storage y cache..."
mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Esperar base de datos (hasta 30s)
echo "Esperando a que la base de datos esté lista..."
for i in $(seq 1 30); do
    if php -d detect_unicode=0 artisan migrate:status > /dev/null 2>&1; then
        break
    fi
    echo "Esperando... ($i)"
    sleep 1
done

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones..."
php artisan migrate --force || true

if php artisan db:seed --class=DummyDataSeeder -q > /dev/null 2>&1; then
    echo "Seeder DummyDataSeeder ejecutado."
else
    echo "Seeder DummyDataSeeder no disponible o falló, continuando..."
fi

echo "Todo listo. Iniciando Apache..."
exec apache2-foreground
