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
    libzip-dev \
  && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
  && rm -rf /var/lib/apt/lists/*

# 3. Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# 4. Instalar Composer (copia el binario oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Directorio de trabajo
WORKDIR /var/www/html

# 6. Instalar dependencias para pruebas
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    gnupg \
    unzip \
    libnss3 \
    libgbm-dev \
    libxshmfence1 \
    libasound2

# 7. Instalar Xdebug para cobertura de pruebas
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# 8. Script para esperar a que la base de datos esté lista
ADD https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

# 9. Copiar el código de Laravel (se hará en docker-compose)
# 10. Configuración de Xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# 11. Ajustar permisos en storage y bootstrap/cache
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# 12. Apuntar DocumentRoot a /public y suprimir warning de ServerName
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 13. Entrypoint y comando de arranque
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]