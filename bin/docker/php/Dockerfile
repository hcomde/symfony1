FROM php:8-cli

COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN pecl install xdebug \
 && docker-php-ext-enable xdebug
