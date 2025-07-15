#!/bin/bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
npm ci
npm run build

# Clear and cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create database and run migrations
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force
