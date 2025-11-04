# Dockerfile alternativo - Instalación de dependencias después del build
FROM php:8.2-apache

# Configurar fuentes HTTPS
RUN printf '%s\n' \
    "deb https://deb.debian.org/debian trixie main contrib non-free" \
    "deb https://deb.debian.org/debian trixie-updates main contrib non-free" \
    "deb https://deb.debian.org/debian-security trixie-security main contrib non-free" \
    > /etc/apt/sources.list && \
        apt-get update -o Acquire::Retries=3 && \
        apt-get install -y --no-install-recommends apt-transport-https ca-certificates gnupg dirmngr \
        && rm -rf /var/lib/apt/lists/*

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update -o Acquire::Retries=3 \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libpq-dev \
        libzip-dev \
        imagemagick \
        libmagickwand-dev \
        default-mysql-client \
        gnupg \
        libnss3 \
        libgbm-dev \
        libxshmfence1 \
        libasound2 \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && pecl install imagick xdebug \
    && docker-php-ext-enable imagick xdebug \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de Xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Directorio de trabajo
WORKDIR /var/www/html

# Script wait-for-it
ADD https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

# NOTA: Las dependencias de composer se instalarán en entrypoint.sh
# Esto evita problemas con archivos que no existen durante el build

# Crear directorios necesarios
RUN mkdir -p storage/app/public storage/app/exports storage/framework/cache \
             storage/framework/sessions storage/framework/views \
             storage/logs bootstrap/cache app/Exports \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Configurar Apache
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copiar entrypoint mejorado
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]