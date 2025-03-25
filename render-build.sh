#!/usr/bin/env bash

# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Exécuter les migrations
php artisan migrate --force

# Générer la clé d'application
php artisan key:generate

# Donner les permissions aux dossiers nécessaires
chmod -R 775 storage bootstrap/cache
