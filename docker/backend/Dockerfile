# 1. Base: Utilizamos PHP 8.2 con Apache, ideal para servir aplicaciones Laravel con mod_rewrite activado.
FROM php:8.2-apache

# 2. Actualización e instalación de dependencias esenciales:
# - libpng-dev: para el manejo de imágenes.
# - libonig-dev: para soporte de expresiones regulares (útil en la manipulación de cadenas).
# - libxml2-dev: para procesar XML.
# - zip y unzip: para manejar archivos comprimidos.
# - libpq-dev: para conectividad con PostgreSQL (base de datos común en ecommerce).
# Además, instalamos las extensiones de PHP necesarias:
# - pdo y pdo_pgsql: para conexiones seguras a la base de datos.
# - mbstring, exif, pcntl, bcmath y gd: para manejo de cadenas, imágenes, procesos, cálculos precisos y manipulación de imágenes.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Habilitar el módulo de reescritura de Apache, imprescindible para URLs amigables y SEO en un ecommerce.
RUN a2enmod rewrite

# 4. Instalar Composer: Copiamos el ejecutable de Composer desde la imagen oficial, para la gestión de dependencias de PHP.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Establecer el directorio de trabajo dentro del contenedor.
WORKDIR /var/www/html

# 6. Copiar el código de la aplicación Laravel al contenedor.
# Es recomendable tener un archivo .dockerignore para omitir archivos innecesarios (por ejemplo, node_modules, vendor, etc.).
COPY . .

# 7. Configuración de permisos:
# Asigna la propiedad de los archivos al usuario www-data (usado por Apache)
# y otorga permisos de escritura a los directorios necesarios (storage y bootstrap/cache),
# lo cual es vital para el correcto funcionamiento de Laravel, especialmente en un entorno de ecommerce.
# 7. Configuración de permisos:
# Se crean los directorios necesarios (si no existen) y se asignan los permisos correspondientes.
RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 8. Exponer el puerto 80 para que Apache pueda servir la aplicación.
EXPOSE 80

# 9. Comando por defecto para iniciar Apache en primer plano.
CMD ["apache2-foreground"]

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]