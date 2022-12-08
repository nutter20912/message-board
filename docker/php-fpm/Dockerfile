FROM php:8.1-fpm-alpine as base

LABEL maintainer="Paul Chou"

ARG WWWGROUP

WORKDIR /var/www

ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk add --no-cache libpng-dev \
  && docker-php-ext-install pcntl gd bcmath

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
COPY composer.* .
RUN composer install --no-scripts && composer clear-cache

###########################################################################
# local
###########################################################################
FROM base AS local

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

COPY . .
RUN chmod -R 777 storage
RUN composer run post-autoload-dump

###########################################################################
# test
###########################################################################
FROM base AS test

COPY . .
RUN chmod -R 777 storage
RUN composer run post-autoload-dump

RUN php artisan test

###########################################################################
# prod
###########################################################################
FROM base AS prod

COPY . .
RUN chmod -R 777 storage
RUN composer run post-autoload-dump