FROM php:8.4-apache-bullseye

RUN apt-get update -y && apt-get install \
        libcurl3-dev \
        libldap2-dev \
        libicu-dev \
        openssh-client \
        git \
        zip \
        libzip-dev \
        libssl-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype6-dev \
        pkg-config \
      -y --no-install-recommends && \
      apt-get clean && \
      rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
      docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype && \
      docker-php-ext-install \
        gd \
        curl \
        zip \
        ldap \
        intl \
        pdo_mysql \
        bcmath \
        sockets

# MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# XDebug
RUN pecl install xdebug-3.4.7 \
  && docker-php-ext-enable xdebug \
  && touch /var/log/xdebug.log \
  && chmod 777 /var/log/xdebug.log

# Composer installation
COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer

COPY xdebug.ini "${PHP_INI_DIR}/conf.d"

COPY apache.ports.conf /etc/apache2/ports.conf
COPY apache.app.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite headers && \
    groupadd --gid 1000 app && \
    adduser --uid 1000 --gid 1000 --disabled-password --gecos '' app

USER app

WORKDIR /var/www/html
