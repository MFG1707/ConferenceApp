#!/usr/bin/env bash

# Afficher les commandes pour debug
set -x

# Vérifier si Composer est installé
if ! command -v composer &> /dev/null
then
    echo "Composer n'est pas installé. Installation en cours..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Installer les dépendances PHP avec Composer
composer install --no-dev --optimize-autoloader

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations (facultatif, à activer si nécessaire)
php artisan migrate --force

# Générer les caches de configuration, routes et vues
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Définir les permissions nécessaires pour le stockage et le cache
chmod -R 777 storage bootstrap/cache

echo "Build terminé avec succès !"
