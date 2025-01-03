ARG PHP_VERSION=7.4.33
ARG COMPOSER_VERSION=2.8.3

# Estágio 1: Build
FROM php:${PHP_VERSION}-fpm-alpine AS builder

RUN apk add --no-cache --virtual .build-deps \
    bash \
    gcc \
    g++ \
    make \
    autoconf \
    zlib-dev \
    bzip2-dev \
    libsodium-dev \
    libxml2-dev \
    libxslt-dev \
    yaml-dev \
    sqlite-dev && \
    docker-php-ext-install \
    bz2 \
    calendar \
    exif \
    opcache \
    pcntl \
    shmop \
    soap \
    sockets \
    sodium \
    sysvsem \
    sysvshm \
    xsl \
    pdo_sqlite && \
    pecl install yaml && docker-php-ext-enable yaml && \
    apk del .build-deps

# Estágio 2: Composer
FROM composer:${COMPOSER_VERSION} AS composer-stage

# Estágio 3: Final 
FROM php:${PHP_VERSION}-fpm-alpine

# Instalar dependências runtime necessárias
RUN apk add --no-cache \
    bash \
    libxslt \
    libxml2 \
    libsodium \
    yaml \
    sqlite \
    bzip2-dev \
    nginx 

RUN apk add gnu-libiconv
ENV LD_PRELOAD=/usr/lib/preloadable_libiconv.so

# Copiar extensões e configurações do estágio builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copiar Composer
COPY --from=composer-stage /usr/bin/composer /usr/bin/composer

# Copiar a aplicação e configuração do Nginx
COPY . /var/www/html
COPY ./nginx/default.conf /etc/nginx/nginx.conf

# Permissões
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Permissao total para o setup-db
RUN chmod 777 /var/www/html/database/setup-db.php

# Instalar dependências do Composer
WORKDIR /var/www/html
RUN composer install --no-interaction --no-progress --no-suggest --optimize-autoloader --verbose
RUN composer dump-autoload --optimize

# Torna o start.sh executável
RUN chmod +x /var/www/html/start.sh

EXPOSE 80

# Executa o start.sh antes de iniciar Nginx e PHP-FPM
CMD ["/bin/bash", "-c", "/var/www/html/start.sh"]
