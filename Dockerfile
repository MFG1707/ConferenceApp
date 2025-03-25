# Utiliser l'image PHP officielle avec les extensions nécessaires
FROM php:8.2-fpm

# Installer les dépendances système (corrigé)
RUN apt-get update && \
    apt-get install -y \
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
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet dans le conteneur
COPY . .

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Donner les permissions aux fichiers nécessaires
RUN chmod -R 777 storage bootstrap/cache

# Exposer le port
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]