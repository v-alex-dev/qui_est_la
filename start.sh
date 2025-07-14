#!/bin/bash

# Copy environment file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate app key if not set
echo "Checking application key..."
php artisan key:generate --force

# Clear and cache configurations
echo "Optimizing Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

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
