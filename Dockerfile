# syntax=docker/dockerfile:1

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# RUN composer install --no-dev --no-scripts --optimize-autoloader \
#         --no-interaction --no-progress --prefer-dist --ignore-platform-reqs
RUN composer install --no-scripts --optimize-autoloader \
        --no-interaction --no-progress --prefer-dist --ignore-platform-reqs

FROM node:22 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy
RUN npm run build

# 3) PHP-FPM application.
FROM php:8.5-fpm AS app
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql pgsql gd zip bcmath intl exif pcntl mbstring \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN chown -R www-data:www-data storage bootstrap/cache
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# 4) Nginx (serves static files, proxies PHP to the app container on :9000).
FROM nginx:alpine AS web
COPY --from=assets /app/public /var/www/html/public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
