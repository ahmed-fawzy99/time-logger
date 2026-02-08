###############################################################################
# Base – shared PHP extensions (built once, reused by later stages)
###############################################################################
FROM dunglas/frankenphp:1-php8.4-alpine AS base

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    icu-dev \
    libxml2-dev \
    libxslt-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        pcntl \
        sockets \
        intl \
        gd \
        zip \
        exif \
        bcmath \
        xsl


###############################################################################
# Stage 1 – Composer dependencies
###############################################################################
FROM base AS composer-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

###############################################################################
# Stage 2 – Frontend assets
###############################################################################
FROM node:22-alpine AS frontend-build

RUN corepack enable && corepack prepare pnpm@latest --activate

WORKDIR /app

COPY package.json pnpm-lock.yaml ./

RUN pnpm install --frozen-lockfile

COPY . .

RUN pnpm run build

###############################################################################
# Stage 3 – Production image
###############################################################################
FROM base AS production

LABEL maintainer="TimeLogger"

COPY docker/php.ini $PHP_INI_DIR/conf.d/99-production.ini

WORKDIR /app

COPY . .
COPY --from=composer-deps /app/vendor ./vendor
COPY --from=composer-deps /app/bootstrap/cache/packages.php ./bootstrap/cache/packages.php
COPY --from=frontend-build /app/public/build ./public/build

RUN mkdir -p storage/logs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache


COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
