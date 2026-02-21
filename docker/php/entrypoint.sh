#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ]; then
  composer install --no-interaction
fi

php spark migrate --all --no-interaction
php spark db:seed DatabaseSeeder

exec php-fpm
