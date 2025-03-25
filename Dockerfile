# Étape 1 : Builder les assets frontend
FROM node:18 as node
WORKDIR /var/www/html
COPY package.json vite.config.js ./
COPY resources ./resources
RUN npm install && npm run build

# Étape 2 : Image PHP-FPM + Nginx
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && \
    apt-get install -y \
        nginx \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libpq-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Configurer Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copier l'application
WORKDIR /var/www/html
COPY . .
COPY --from=node /var/www/html/public/build ./public/build

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage

# Exposer le port HTTP
EXPOSE 80

# Commande de démarrage
CMD bash -c "php-fpm -D && nginx -g 'daemon off;'"