#!/bin/bash

# Créer le fichier de base de données SQLite s'il n'existe pas
echo "Setting up SQLite database..."
touch /var/www/html/database/database.sqlite
chmod 664 /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/database.sqlite

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Run seeders if this is a fresh deployment
echo "Running database seeders..."
php artisan db:seed --force

# Start Apache
echo "Starting Apache..."
apache2-foreground
