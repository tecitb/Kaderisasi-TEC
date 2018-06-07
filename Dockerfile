FROM php:7.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html
MAINTAINER tec_itb@km.itb.ac.id