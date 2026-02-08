#!/bin/sh
set -e

if [ ! -f /app/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /app/.env.example /app/.env
fi

echo "Caching configuration..."
php artisan optimize

echo "Running database migrations..."
php artisan migrate --force

echo "Ensuring storage link exists..."
php artisan storage:link 2>/dev/null || true

echo "Starting Octane with FrankenPHP..."
exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000
