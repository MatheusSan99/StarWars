# Estágio para PHP 7.4 com Xdebug
FROM php:7.4-cli-alpine AS php74

# Instalar dependências necessárias
RUN apk add --no-cache \
    bash \
    bzip2-dev libsodium-dev libxml2-dev libxslt-dev \
    linux-headers yaml-dev sqlite-dev \
    gcc make g++ zlib-dev autoconf nginx \
    openssl expect curl && \
    docker-php-ext-install \
    bz2 calendar exif opcache pcntl shmop soap \
    sockets sodium sysvsem sysvshm xsl pdo_sqlite

# Instalar o GNU iconv no PHP
RUN apk add gnu-libiconv
ENV LD_PRELOAD="/usr/lib/preloadable_libiconv.so php-fpm php"
RUN php -r '$res = iconv("utf-8", "utf-8//IGNORE", "fooą");'

# Instalar o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar os arquivos do diretório atual para o container
COPY . /var/www/html

# Garantir permissões corretas
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

# Permissão scripts    
RUN chmod +x /var/www/html/scripts/setup-php.sh
RUN chmod +x /var/www/html/start.sh

# Copiar a configuração do Nginx
COPY ./nginx/nginx.conf /etc/nginx/conf.d/nginx.conf

# Expor a porta 80 para o Nginx
EXPOSE 80

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Instalar Dependencias composer
RUN composer install
# Atualizar o autoloload
RUN composer dump-autoload

# Comando para iniciar o PHP com Nginx
CMD ["/var/www/html/start.sh"]
