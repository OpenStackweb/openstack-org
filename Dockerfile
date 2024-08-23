FROM php:7.2-fpm

ARG DEBIAN_FRONTEND=noninteractive
ARG NVM_VERSION="v0.39.7"
ARG GITHUB_OAUTH_TOKEN
ARG XDEBUG_VERSION="xdebug-3.1.6"

ENV NVM_VERSION=$NVM_VERSION
ENV NODE_VERSION="12.22.12"
ENV NVM_DIR=/root/.nvm
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV GITHUB_OAUTH_TOKEN=$GITHUB_OAUTH_TOKEN
ENV PHP_DIR /usr/local/etc/php

# base packages
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    redis-tools \
    nano \
    python3 \
    make \
    g++\
    gpg \
    gettext \
    libgmp-dev

RUN apt clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring exif pcntl bcmath sockets gettext gmp gd mysqli
RUN docker-php-ext-enable gd mysqli
# XDEBUG
RUN yes | pecl install ${XDEBUG_VERSION}
COPY docker-compose/php/docker-php-ext-xdebug.ini $PHP_DIR/conf.d/docker-php-ext-xdebug.ini

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN echo 'memory_limit = 512M' >> $PHP_INI_DIR/php.ini;

# nvm

RUN mkdir $NVM_DIR  \
    && curl https://raw.githubusercontent.com/creationix/nvm/$NVM_VERSION/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH      $NVM_DIR/v$NODE_VERSION/bin:$PATH

# yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt update && apt install -y yarn

# install node
RUN curl -fsSL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs

WORKDIR /var/www
COPY . /var/www
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer config -g github-oauth.github.com $GITHUB_OAUTH_TOKEN
RUN git config --global --add safe.directory /var/www