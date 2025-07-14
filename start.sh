#!/bin/bash

echo "Starting Laravel application with PostgreSQL..."

# Wait for database to be ready
echo "Waiting for database to be ready..."
for i in {1..30}; do
    if timeout 10 php artisan migrate:status &>/dev/null; then
        echo "Database connection successful!"
        break
    else
        echo "Database connection attempt $i/30 failed, retrying in 5s..."
        if [ $i -eq 30 ]; then
            echo "ERROR: Database connection failed after 30 attempts. Check your environment variables:"
            echo "DB_HOST: ${DB_HOST}"
            echo "DB_PORT: ${DB_PORT}"
            echo "DB_DATABASE: ${DB_DATABASE}"
            echo "DB_USERNAME: ${DB_USERNAME}"
            echo "DB_CONNECTION: ${DB_CONNECTION}"
            exit 1
        fi
        sleep 5
    fi
done

# Clear and cache configuration
echo "Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Start Apache on port 80
echo "Starting Apache on port 80..."
exec apache2-foreground
