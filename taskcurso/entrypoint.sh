#!/bin/sh

# Esperar a que PostgreSQL esté disponible (hasta 30s)
echo "Esperando a que la base de datos esté lista..."
for i in $(seq 1 30); do
    php artisan migrate:status > /dev/null 2>&1 && break
    echo "Esperando... ($i)"
    sleep 1
done

# Instalar dependencias si no existen
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --prefer-dist --no-interaction --optimize-autoloader
fi

# Migraciones + Seeder
echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeder de datos dummy..."
php artisan db:seed --class=DummyDataSeeder

# Generar clave de aplicación si no existe
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Configurar almacenamiento
php artisan storage:link

echo "Todo listo. Iniciando Apache..."
exec apache2-foreground