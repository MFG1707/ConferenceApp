# Étape 1: Builder PHP
FROM php:8.2-fpm

# Installer les dépendances
RUN apt-get update && \
    apt-get install -y \
        nginx \
        unzip \
        curl \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html
COPY . .

# Installer les dépendances (sans les dépendances de développement)
RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]