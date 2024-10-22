ARG PHP_VERSION
ARG COMPOSER_VERSION

FROM composer:${COMPOSER_VERSION} AS composer
FROM php:${PHP_VERSION}-fpm AS php-fpm

RUN apt update && apt install -y \
    procps \
    acl \
    apt-transport-https \
    build-essential \
    ca-certificates \
    coreutils \
    curl \
    file \
    gettext \
    git \
    wget \
    zip \
    unzip \
    libssl-dev \
    sshpass

# Setup Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Setup extensões
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions xdebug sockets mysqli pdo_mysql intl zip
