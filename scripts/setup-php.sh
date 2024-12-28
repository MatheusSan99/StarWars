#!/bin/bash

set -e  # Para falhar em caso de erros
set -x  # Para debug

CONTAINER_PHP_DIR="/var/www/html"
SCRIPTS_DIR="/var/www/html/scripts"
PHP_INI_DIR="/var/www/html/php"

echo "Variável DEV_ENV: $DEV_ENV"

if [ "$DEV_ENV" = "true" ]; then
    echo "Ambiente de desenvolvimento detectado..."

    if php -m | grep -q xdebug; then
        echo "Xdebug já está instalado, pulando a instalação..."
        exit 0
    fi

    echo "Instalando dependências necessárias para o Xdebug..."
    apk add --no-cache \
        gcc \
        make \
        autoconf \
        g++ \
        zlib-dev \
        libxml2-dev \
        libxslt-dev || { echo "Erro ao instalar dependências"; exit 0; }

    echo "Instalando Xdebug 3.1.6..."
    pecl install xdebug-3.1.6 || { echo "Erro ao instalar o Xdebug"; exit 0; }

    if command -v docker-php-ext-enable &>/dev/null; then
        docker-php-ext-enable xdebug || { echo "Erro ao habilitar o Xdebug"; exit 0; }
    else
        echo "Aviso: docker-php-ext-enable não encontrado. Verifique a configuração manual do Xdebug."
    fi

    cp "$PHP_INI_DIR/php.ini-development" /usr/local/etc/php/php.ini

    # Remove pacotes de build desnecessários
    echo "Removendo dependências de build..."
    apk del --no-cache \
        gcc \
        make \
        autoconf \
        g++ \
        zlib-dev \
        libxml2-dev \
        libxslt-dev || { echo "Erro ao remover dependências"; exit 0; }
else
    echo "Ambiente de produção detectado, Xdebug não será instalado..."

    cp "$PHP_INI_DIR/php.ini-production" /usr/local/etc/php/php.ini
fi
