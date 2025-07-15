#!/bin/bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm ci
npm run build

# Create database directory and file
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite

# Clear and cache configurations for production
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
php artisan db:seed --force
