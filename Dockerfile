FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize

FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=vendor /app ./
RUN npm run build

FROM php:8.4-cli-alpine AS app

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev \
    unzip \
    zip \
    && docker-php-ext-install \
    bcmath \
    intl \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app ./
COPY --from=assets /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 8000

ENTRYPOINT ["entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
