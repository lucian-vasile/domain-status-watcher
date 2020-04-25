FROM php:7.4-alpine

RUN apk upgrade --no-cache
RUN apk add --no-cache icu-libs
RUN apk add --no-cache --virtual .php-ext-build-deps curl \
    libsodium-dev \
    icu-dev
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS
RUN pecl install redis
RUN docker-php-ext-install -j $(nproc) bcmath intl pdo_mysql sodium
RUN docker-php-ext-enable redis
