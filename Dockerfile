FROM php:7.4-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80