FROM php:8.2-fpm-alpine

ENV NODE_VERSION=${NODE_VERSION:-20.0.0}

RUN apk add git openssh-client
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo pdo_mysql pdo_dblib bcmath gd zip redis imagick pcntl sqlsrv pdo_sqlsrv #mongodb

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
