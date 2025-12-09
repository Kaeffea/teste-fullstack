#!/bin/bash
set -e

# Garante que as pastas existem
mkdir -p /var/www/html/app/tmp/cache/models
mkdir -p /var/www/html/app/tmp/cache/persistent
mkdir -p /var/www/html/app/tmp/cache/views

mkdir -p /var/www/html/app/webroot/files/uploads

# Ajusta dono e permissão (pra não dar erro no cache / uploads)
chown -R www-data:www-data /var/www/html/app/tmp /var/www/html/app/webroot/files
chmod -R 777 /var/www/html/app/tmp /var/www/html/app/webroot/files

# Sobe o Apache (mesmo comando que a imagem oficial usa)
exec apache2-foreground
