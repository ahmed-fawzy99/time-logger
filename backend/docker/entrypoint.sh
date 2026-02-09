#!/bin/sh
set -e

KEY_FILE="/app/storage/app/.app_key"

if [ ! -f /app/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /app/.env.example /app/.env
fi

# Restore persisted key, or generate and persist a new one
if [ -f "$KEY_FILE" ]; then
    echo "Restoring application key from volume..."
    sed -i "s|^APP_KEY=.*|APP_KEY=$(cat $KEY_FILE)|" /app/.env
elif [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" /app/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
    grep "^APP_KEY=" /app/.env | cut -d'=' -f2- > "$KEY_FILE"
fi

# Export so it overrides the empty env var from Docker Compose env_file
export APP_KEY=$(grep "^APP_KEY=" /app/.env | cut -d'=' -f2-)

echo "Caching configuration..."
php artisan optimize

echo "Running database migrations..."
php artisan migrate --force

echo "Ensuring storage link exists..."
php artisan storage:link 2>/dev/null || true

echo "Starting Octane with FrankenPHP..."
exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000
