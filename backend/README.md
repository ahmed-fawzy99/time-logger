# API Backend For Time Logger

## Standalone Installation:

> Note: This README addresses only the backend API in case you want to run it separately. If you want to run the entire
> application with the frontend, please refer to the main README in the root directory.

```bash
git clone https://github.com/ahmed-fawzy99/time-logger.git
cd time-logger/backend
```

### Using Docker:

```bash

cp .env.example .env # if you want to use different DB credentials, update your .env file

docker compose up -d --build

docker compose exec -it app php artisan key:generate
docker compose exec -it app php artisan db:seed 
```

If you want to fill the database with records for testing, use this command instead:

```bash
docker compose exec -it app php artisan db:seed --class=Database\\Seeders\\TestSeeder

# To undo: 
docker compose exec -it app php artisan migrate:fresh --seed 
```

### Non-Docker:

Requirements:

- PHP 8.4
- Composer
- PostgreSQL
- Redis
- FrankenPHP (optional)

```bash
composer install --no-dev

cp .env.example .env  # if you want to use different default settings, update your .env file before running the next command

```

Open the .env file and update the database credentials to match your PostgreSQL setup. Also, ensure that Redis is
running and update the Redis connection settings if necessary.

```bash
php artisan key:generate

php artisan optimize
php artisan storage:link 

php artisan migrate --seed

# Run the server (with FrankenPHP):
php artisan octane:frankenphp --host=0.0.0.0 --port=8000

# Or run the server with the built-in PHP server:
php artisan serve
```

If you want to fill the database with records for testing, use this command instead:

```bash
php artisan db:seed --class=Database\\Seeders\\TestSeeder

# To undo: 
php artisan migrate:fresh --seed 
```
