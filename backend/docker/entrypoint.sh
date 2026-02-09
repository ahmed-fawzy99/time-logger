#!/bin/sh
set -e

if [ ! -f /app/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /app/.env.example /app/.env

    if [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" /app/.env; then
        echo "Generating application key..."
        php artisan key:generate --force
    fi

    # Export the generated key so it overrides the empty env var from Docker Compose env_file
    export APP_KEY=$(grep "^APP_KEY=" /app/.env | cut -d'=' -f2-)
fi


echo "Caching configuration..."
php artisan optimize

echo "Running database migrations..."
php artisan migrate --force

echo "Ensuring storage link exists..."
php artisan storage:link 2>/dev/null || true

echo "Starting Octane with FrankenPHP..."
exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000
