#!/usr/bin/env bash

set -e

# Rodar o script de setup
/var/www/html/scripts/setup-php.sh

# Iniciar o PHP-FPM
php-fpm
# Iniciar o Nginx
nginx -g "daemon off;"
