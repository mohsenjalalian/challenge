
## Installation

* git clone challenge project

* run docker-compose up -d in the root dir

* update your /etc/hosts and add back.ch domain or change 
vhost domain in docker/ch-nginx/vhosts.d

* run docker-compose exec ch-php-cli bash

* copy .env.example and make your env file

* run composer install

* run chown -R www-data:www-data /src/storage

* run php artisan migrate in /src dir

* run php artisan create:products-index

* import postman collection file from docs dir
    
* run php artisan  queue:work --queue=bulk_product
