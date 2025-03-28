#!/bin/bash

until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  echo "Waitting for database to be ready..."
  sleep 2
done

php bin/console doctrine:database:create --if-not-exists

php bin/console doctrine:migrations:migrate --no-interaction
chown -R www-data:www-data /var/www/symfony/var

exec "php-fpm"