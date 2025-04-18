#!/bin/sh

# Si no existe el directorio vendor, instala las dependencias con Composer
if [ ! -d "/var/www/html/vendor" ]; then
    echo "Directorio vendor no encontrado, instalando dependencias..."
    composer install --prefer-dist --no-interaction --optimize-autoloader
fi

# Realiza las migraciones de la base de datos
echo "Ejecutando migraciones..."
php artisan migrate --force
php artisan db:seed --class=DummyDataSeeder

# Inicia Apache en primer plano (esto reemplaza el proceso actual)
exec apache2-foreground

# Esta línea no se ejecutará debido a exec
echo "Migraciones aplicadas correctamente."

