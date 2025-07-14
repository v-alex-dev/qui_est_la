#!/bin/bash

# Wait for database to be ready
echo "Waiting for database connection..."
until php artisan migrate:status &>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done

# Run migrations
echo "Running database migrations..."
php artisan migrate

# Clear and cache configuration
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
