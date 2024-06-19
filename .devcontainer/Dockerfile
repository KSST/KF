FROM mcr.microsoft.com/devcontainers/php:8

# FROM php:8-cli
# FROM php:8-apache

# Setup system dependecies
RUN apt-get update && apt-get install -y \
    # for PHP extension: intl
    libicu-dev \
    # for PHP extension: gettext
    gettext

# Setup PHP dependencies
RUN docker-php-ext-install \
    gettext \
    intl \
    pdo \
    pdo_mysql

# Update Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer self-update
