#!/usr/bin/env bash

# Afficher les commandes pour debug
set -x

# Installer les dépendances PHP avec Composer
composer install --no-dev --optimize-autoloader

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations (facultatif, à activer si nécessaire)
php artisan migrate --force

# Définir les permissions nécessaires pour le stockage et le cache
chmod -R 777 storage bootstrap/cache

echo "Build terminé avec succès !"

