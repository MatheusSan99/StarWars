#!/bin/bash

set -e  # Para falhar em caso de erros
set -x  # Para debug

CONTAINER_PHP_DIR="/var/www/html"
SCRIPTS_DIR="/var/www/html/scripts"
PHP_INI_DIR="/var/www/html/php/conf.d"

if [ "$DEV_ENV" = "true" ]; then
    echo "Ambiente de desenvolvimento detectado..."

    if php -m | grep -q xdebug; then
        echo "Xdebug já está instalado"
        exit 0
    fi

    #Copiar PHP Development INI

    cp "$PHP_INI_DIR/php.ini-development" /usr/local/etc/php/php.ini

    cd "$CONTAINER_PHP_DIR"
    
    echo "Instalando xdebug..."

    apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.9.2
else
    echo "Ambiente de produção detectado, xdebug nao sera instalado..."

    #Copiar PHP Production INI

    cp "$PHP_INI_DIR/php.ini-production" /usr/local/etc/php/php.ini
fi

#Instalar session se nao tiver instalado
if ! php -m | grep -q session; then
    echo "Instalando session..."
    apk add --no-cache php7-session || { echo "Erro ao instalar o session"; exit 0; }
fi

#Instalar Pdo se nao tiver instalado
if ! php -m | grep -q pdo; then
    echo "Instalando pdo..."
    apk add --no-cache php7-pdo || { echo "Erro ao instalar o pdo"; exit 0; }
fi

#Instalar Nginx se nao tiver instalado
if ! apk info --installed nginx >/dev/null 2>&1; then
    echo "Instalando nginx..."
    apk add --no-cache nginx || { echo "Erro ao instalar o nginx"; exit 0; }
fi

#Instalar phpfmp se nao tiver instalado
if ! apk info --installed php7-fpm >/dev/null 2>&1; then
    echo "Instalando php7-fpm..."
    apk add --no-cache php-fpm || { echo "Erro ao instalar o php7-fpm"; exit 0; }
fi

#Deletar Dependencias desnescessarias que sao utilizadas apenas na compilacao
if apk info --installed gcc >/dev/null 2>&1; then
    apk del gcc make g++ linux-headers autoconf
fi
