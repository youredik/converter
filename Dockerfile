FROM php:8.2.11-fpm-alpine

RUN mkdir /app
WORKDIR /app

# PHP extensions
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s  \
    gd zip xdebug openssl

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer