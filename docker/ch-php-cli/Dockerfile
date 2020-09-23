###################################################################### common

FROM php:7.4.5-cli


RUN apt-get update \
    && apt-get install git zip unzip python3.7 python3-pip vim htop iputils-ping wget cron -y

RUN pip3 install supervisor


RUN docker-php-ext-install pdo_mysql bcmath sockets

# install composer
RUN curl https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# create php.ini
COPY ./docker/ch-php-cli/php.ini /usr/local/etc/php/php.ini

RUN pecl install redis &&  docker-php-ext-enable redis

#supervisor
COPY ./docker/ch-php-cli/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/ch-php-cli/conf.d /etc/supervisor/conf.d
ENTRYPOINT supervisord -n -c /etc/supervisor/supervisord.conf

WORKDIR /src

