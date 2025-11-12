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
    libzip-dev \
    libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
  && rm -rf /var/lib/apt/lists/*

# 3. Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# 4. Instalar Composer (copia el binario oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Directorio de trabajo (el contexto de build ya es ./taskcurso)
WORKDIR /var/www/html

# 6. Copiar el código del subproyecto Laravel (por contexto de build)
COPY . .

# 7. Configurar Composer y instalar dependencias
RUN composer self-update \
 && composer clear-cache \
 && composer update psr/cache --no-interaction --ansi || true \
 && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
 && composer dump-autoload -o

# 8. Publicar configuraciones de vendors
RUN php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config" || true \
 && php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --force || true

# 9. Ajustar permisos en storage, bootstrap/cache y vendor
RUN mkdir -p storage bootstrap/cache vendor \
 && chown -R www-data:www-data storage bootstrap/cache vendor \
 && chmod -R 775 storage bootstrap/cache vendor

# 10. Apuntar DocumentRoot a /public y suprimir warning de ServerName
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 11. Exponer el puerto 80 para Apache
EXPOSE 80

# 12. Entrypoint y comando de arranque
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
