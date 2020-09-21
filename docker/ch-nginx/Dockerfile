FROM nginx:1.17.6

RUN apt-get update \
    && apt-get install vim -y

# copy host config file to container
COPY ./docker/ch-nginx/nginx.conf /etc/nginx/nginx.conf

# copy php_listen config file to container
COPY ./docker/ch-nginx/php_listen.conf /etc/nginx/php_listen.conf

# copy index.html to container
COPY ./docker/ch-nginx/index.html /src/default/index.html

# copy host config file to container
COPY ./docker/ch-nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

#vhosts
COPY ./docker/ch-nginx/vhosts.d /etc/nginx/vhosts.d

