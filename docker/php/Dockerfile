FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    bash \
    curl \
    git \
    linux-headers \
    openssl-dev

RUN docker-php-ext-install intl pdo pdo_mysql mbstring zip
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-interaction
RUN chmod +x /var/www/html/docker/php/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

EXPOSE 9000

CMD ["/var/www/html/docker/php/entrypoint.sh"]
