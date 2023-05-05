#!/usr/bin/env sh
composer run post-autoload-dump

php artisan key:generate
php artisan l5-swagger:generate
php artisan telescope:install
php artisan horizon:install

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
