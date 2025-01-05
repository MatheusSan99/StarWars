#!/usr/bin/env bash

set -e

# Diretórios de certificados SSL
SSL_DIR="/etc/nginx/ssl"

# Verificar se o diretório existe, caso contrário, criá-lo
if [ ! -d "$SSL_DIR" ]; then
    mkdir -p "$SSL_DIR"
fi

# Verificar se o certificado já foi gerado, caso contrário, gerar um novo
if [ ! -f "$SSL_DIR/selfsigned.crt" ] || [ ! -f "$SSL_DIR/selfsigned.key" ]; then
    echo "Gerando certificado autoassinado..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout "$SSL_DIR/selfsigned.key" \
        -out "$SSL_DIR/selfsigned.crt" \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"
    echo "Certificado autoassinado gerado em $SSL_DIR"
else
    echo "Certificado já existe, pulando geração..."
fi

# Rodar o script de setup
/var/www/html/scripts/setup-php.sh

# Iniciar o PHP-FPM
php-fpm &

# Iniciar o Nginx
nginx -g "daemon off;"
