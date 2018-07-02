FROM php:7.1-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN touch .env
COPY . /var/www/html
RUN mkdir -p /var/www/html/uploads
RUN mkdir -p /var/www/html/uploads/profile
RUN mkdir -p /var/www/html/uploads/assignment
RUN chmod -R 766 /var/www/html/uploads

RUN chown -R www-data:www-data /var/www/html/uploads

MAINTAINER tec_itb@km.itb.ac.id