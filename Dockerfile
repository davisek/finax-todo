FROM php:8.4-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    unzip \
    curl

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
