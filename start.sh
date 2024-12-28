#!/usr/bin/env bash

set -e  # Para falhar em caso de erros

# Rodar o script de setup
/var/www/html/scripts/setup-php.sh

php -S 0.0.0.0:80 -t /var/www/html/