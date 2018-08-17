FROM php:7.2-apache
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd

RUN a2enmod rewrite
RUN touch .env
COPY . /var/www/html

MAINTAINER tec_itb@km.itb.ac.id
