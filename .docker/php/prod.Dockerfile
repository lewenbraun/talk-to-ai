FROM php:8.4-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache --virtual .build-deps \
    ca-certificates \
    gcc \
    libc-dev \
    make \
    musl-dev \
    postgresql-dev \
    zip \
    unzip \
    git \
    nano \
    php-redis \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql opcache pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY .docker/supervisor/laravel-worker.conf /etc/supervisor/laravel-worker.conf

COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN apk add --no-cache nodejs npm

RUN npm install

RUN rm -rf public/build/* public/manifest.json

RUN npm run build

EXPOSE 9000
CMD ["supervisord", "-c", "/etc/supervisor/laravel-worker.conf", "-n"]