# Étape 1: Builder les assets
FROM node:18 as node

WORKDIR /var/www/html
COPY package.json webpack.mix.js ./
COPY resources ./resources

RUN npm install && npm run prod

# Étape 2: Image PHP
FROM php:8.2-fpm

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

# Configuration Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY --from=node /var/www/html/public/js ./public/js
COPY --from=node /var/www/html/public/css ./public/css

RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage

EXPOSE 80

CMD bash -c "php-fpm -D && nginx -g 'daemon off;'"