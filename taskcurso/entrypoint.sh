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

# Generar clave de aplicación si no existe
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Configurar almacenamiento
php artisan storage:link

echo "Todo listo. Iniciando Apache..."
exec apache2-foreground

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Esperar a que la base de datos esté disponible
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "⏳ Esperando a la base de datos en $DB_HOST:$DB_PORT..."
    /wait-for-it.sh "$DB_HOST:$DB_PORT" -t 60 -- echo "✅ Base de datos disponible"
fi

# Verificar si composer.json existe
if [ -f "composer.json" ]; then
    echo "📦 Verificando dependencias de Composer..."
    
    # Verificar si vendor existe
    if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
        echo "📥 Instalando dependencias de Composer..."
        COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --optimize-autoloader
    else
        echo "✅ Dependencias ya instaladas"
    fi
    
    # Verificar si maatwebsite/excel está instalado
    if ! composer show | grep -q "maatwebsite/excel"; then
        echo "📊 Instalando Maatwebsite Excel..."
        COMPOSER_MEMORY_LIMIT=-1 composer require maatwebsite/excel --no-interaction
        
        # Publicar configuración
        if [ -f "artisan" ]; then
            php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --force || true
        fi
    else
        echo "✅ Maatwebsite Excel ya está instalado"
    fi
    
    # Verificar otros paquetes necesarios
    if ! composer show | grep -q "laravel-daily/laravel-charts"; then
        echo "📈 Instalando Laravel Charts..."
        COMPOSER_MEMORY_LIMIT=-1 composer require laravel-daily/laravel-charts --no-interaction || true
    fi
    
    if ! composer show | grep -q "barryvdh/laravel-dompdf"; then
        echo "📄 Instalando DomPDF..."
        COMPOSER_MEMORY_LIMIT=-1 composer require barryvdh/laravel-dompdf --no-interaction || true
    fi
else
    echo "⚠️  No se encontró composer.json, saltando instalación de dependencias"
fi

# Verificar si artisan existe antes de ejecutar comandos
if [ -f "artisan" ]; then
    echo "🔧 Configurando Laravel..."
    
    # Generar key si no existe
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        echo "🔑 Generando APP_KEY..."
        php artisan key:generate --force || true
    fi
    
    # Crear directorios necesarios
    echo "📁 Creando directorios..."
    mkdir -p storage/app/public storage/app/exports storage/framework/{cache,sessions,views} storage/logs bootstrap/cache app/Exports
    
    # Publicar configuraciones de paquetes si no existen
    if [ ! -f "config/excel.php" ]; then
        echo "📋 Publicando configuración de Excel..."
        php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config --force || true
    fi
    
    if [ ! -f "config/cloudinary.php" ]; then
        echo "☁️  Publicando configuración de Cloudinary..."
        php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config" --force || true
    fi
    
    # Limpiar caché
    echo "🧹 Limpiando caché..."
    php artisan config:clear || true
    php artisan cache:clear || true
    php artisan view:clear || true
    php artisan route:clear || true
    
    # Cachear configuración en producción
    if [ "$APP_ENV" = "production" ]; then
        echo "⚡ Optimizando para producción..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
    fi
    
    # Ejecutar migraciones si se especifica
    if [ "$RUN_MIGRATIONS" = "true" ]; then
        echo "🗄️  Ejecutando migraciones..."
        php artisan migrate --force || true
    fi
    
    # Crear link simbólico de storage
    if [ ! -L "public/storage" ]; then
        echo "🔗 Creando enlace simbólico de storage..."
        php artisan storage:link || true
    fi
fi

# Ajustar permisos
echo "🔒 Ajustando permisos..."
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Verificar instalación de Excel
if [ -f "artisan" ]; then
    echo "✅ Verificando instalación de Maatwebsite Excel..."
    if php -r "require 'vendor/autoload.php'; echo class_exists('Maatwebsite\Excel\Facades\Excel') ? 'OK' : 'FAIL';" | grep -q "OK"; then
        echo -e "${GREEN}✅ Maatwebsite Excel está instalado y funcional${NC}"
    else
        echo -e "${YELLOW}⚠️  Maatwebsite Excel podría no estar instalado correctamente${NC}"
    fi
fi

echo "🎉 Contenedor listo!"
echo "----------------------------------------"
