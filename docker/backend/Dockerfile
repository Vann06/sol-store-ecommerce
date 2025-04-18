

# 1. Base: PHP 8.2 con Apache
FROM php:8.2-apache

# 2. Instalar dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
  && rm -rf /var/lib/apt/lists/*

# 3. Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# 4. Instalar Composer (copia el binario oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar el código de Laravel
#    Asume que tienes un .dockerignore para omitir /vendor, /node_modules, etc.
COPY . .

# 7. Instalar dependencias de Composer y Pail
RUN composer install --no-dev --optimize-autoloader \
 && composer require laravel/pail --no-interaction \
 && composer require doctrine/dbal --no-interaction \
 && composer dump-autoload -o

# 8. Ajustar permisos en storage y bootstrap/cache
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# 9. Apuntar DocumentRoot a /public y suprimir warning de ServerName
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 10. Exponer el puerto 80 para Apache
EXPOSE 80

# 11. Entrypoint y comando de arranque
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
