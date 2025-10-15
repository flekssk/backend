FROM php:8.4.1-fpm-alpine AS base

# Системные пакеты и зависимости для сборки PHP-расширений
RUN apk update && apk add --no-cache \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    curl \
    libpq-dev \
    $PHPIZE_DEPS

# Устанавливаем необходимые PHP-расширения
RUN docker-php-ext-install pdo_mysql zip dom \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY .. .

RUN composer install --no-dev --optimize-autoloader --prefer-dist

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
       && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

FROM base AS api
EXPOSE 8000
CMD ["php-fpm"]

FROM base AS worker
CMD ["php", "artisan", "queue:work", "--sleep=3", "--tries=3"]

FROM base AS websocket
RUN docker-php-ext-install pcntl
CMD ["php", "artisan", "reverb:start"]
