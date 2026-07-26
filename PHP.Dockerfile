FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash \
  && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer