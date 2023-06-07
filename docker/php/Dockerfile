FROM php:8.1-fpm-alpine
RUN apk update
RUN apk add oniguruma-dev
WORKDIR /var/www
# lumen/laravel packages
RUN docker-php-ext-install mbstring

# composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

