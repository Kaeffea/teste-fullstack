FROM php:7.4-apache

# Extensões do PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Mod rewrite pro Cake
RUN a2enmod rewrite

# (opcional) Dono padrão da pasta, antes do volume montar
RUN chown -R www-data:www-data /var/www/html

# Copia o entrypoint que vai ajustar permissão nas pastas do Cake
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

# Usa o entrypoint customizado
ENTRYPOINT ["docker-entrypoint.sh"]
