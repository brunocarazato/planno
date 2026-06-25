FROM php:8.3-fpm

ARG WWWUSER=1000
ARG WWWGROUP=1000

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        git \
        libicu-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        bcmath \
        dom \
        intl \
        pdo_mysql \
        xml \
        xmlwriter \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/99-planno.ini
COPY .docker/entrypoints/app.sh /usr/local/bin/planno-app

RUN chmod +x /usr/local/bin/planno-app \
    && groupmod -o -g "${WWWGROUP}" www-data \
    && usermod -o -u "${WWWUSER}" -g www-data www-data

USER www-data

ENTRYPOINT ["planno-app"]
CMD ["php-fpm"]
