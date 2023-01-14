#!/usr/bin/env sh
composer run post-autoload-dump

php artisan key:generate
php artisan l5-swagger:generate

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
