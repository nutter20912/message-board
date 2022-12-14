#!/usr/bin/env sh
composer run post-autoload-dump

php artisan key:generate
php artisan l5-swagger:generate

php-fpm