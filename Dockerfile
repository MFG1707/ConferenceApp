FROM php:8.2-fpm

# 1. Mettre à jour les paquets et installer les dépendances système
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \  # <-- Ajouté pour PostgreSQL
    && rm -rf /var/lib/apt/lists/*  # Nettoyer le cache

# 2. Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql

# 3. Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . .

# 4. Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# 5. Permissions
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]