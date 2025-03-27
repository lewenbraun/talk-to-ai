FROM php:8.4-fpm-alpine

WORKDIR /var/www

# Install required dependencies and PHP extensions
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
    php-redis \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql opcache pcntl

# Copy Composer from the official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Supervisor configuration for Laravel worker
COPY .docker/supervisor/laravel-worker.conf /etc/supervisor/laravel-worker.conf

# Copy Composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy the rest of the project code and optimize the autoloader
COPY . .
RUN composer dump-autoload --optimize

# Copy local PHP configuration
COPY .docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy the entrypoint script and make it executable
COPY .docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

# Use the entrypoint script to adjust permissions before starting the main process
ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/laravel-worker.conf", "-n"]
