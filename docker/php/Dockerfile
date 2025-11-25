FROM php:8.4-fpm-alpine AS base

# Системные пакеты и зависимости для сборки PHP-расширений
RUN apk update && apk add --no-cache \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    curl \
    libpq-dev \
    linux-headers \
    $PHPIZE_DEPS

# Устанавливаем необходимые PHP-расширения
RUN docker-php-ext-install pdo_mysql zip dom sockets pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis

ENV TZ=Europe/Moscow
RUN ln -snf /usr/share/zoneinfo/Europe/Moscow /etc/localtime \
    && echo "Europe/Moscow" > /etc/timezone \
    && echo "date.timezone=Europe/Moscow" > /usr/local/etc/php/conf.d/99-timezone.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY .. .

RUN composer install --no-dev --optimize-autoloader --prefer-dist

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
       && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

FROM base AS api

ARG RR_VERSION=2024.3.0

RUN set -eux; \
    rm -f /usr/local/bin/rr; \
    php vendor/bin/rr get-binary -n --stability=stable --location /usr/local/bin; \
    chmod +x /usr/local/bin/rr; \
    /usr/local/bin/rr --version

EXPOSE 8000
CMD ["php","artisan","octane:start","--server=roadrunner","--host=0.0.0.0","--port=8000","--workers=auto","--max-requests=1000","--no-interaction"]


FROM base AS worker
CMD ["php", "artisan", "queue:work", "--sleep=3", "--tries=3"]

FROM base AS websocket

CMD ["php", "artisan", "reverb:start"]

FROM base AS scheduler

RUN printf "* * * * * cd /var/www && /usr/local/bin/php artisan schedule:run >> /var/log/cron.log 2>&1\n" > /etc/crontabs/root \
    && chmod 0644 /etc/crontabs/root

CMD ["crond", "-f", "-l", "8"]
