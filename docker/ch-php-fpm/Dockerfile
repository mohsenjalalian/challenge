###################################################################### common

FROM php:7.4.5-fpm

RUN apt-get update \
    && apt-get install \
    git \
    libzip-dev \
    zip \
    unzip \
    vim \
    libfreetype6-dev \
    -y

RUN docker-php-ext-install pdo_mysql mysqli bcmath sockets zip


# create php.ini
COPY ./docker/ch-php-fpm/php.ini /usr/local/etc/php/php.ini

RUN pecl install redis &&  docker-php-ext-enable redis

# create www.conf
COPY ./docker/ch-php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# disable access log
COPY ./docker/ch-php-fpm/docker.conf /usr/local/etc/php-fpm.d/docker.conf

# install composer
RUN curl https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /src
