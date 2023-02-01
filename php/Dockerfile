#FROM php:8.1-rc-fpm-alpine
#
#RUN apk update && apk add --no-cache curl zip unzip
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
#CMD ["php-fpm", "-F"]
#
#WORKDIR /var/www
#
#EXPOSE 9000

FROM php:8.1-rc-fpm-alpine

RUN apk update && apk add --no-cache curl zip unzip
RUN docker-php-ext-install pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["php-fpm", "-F"]

WORKDIR /var/www

EXPOSE 9000
