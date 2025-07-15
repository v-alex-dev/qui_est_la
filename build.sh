#!/bin/bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm install
npm run build

# Create SQLite database if it doesn't exist
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
