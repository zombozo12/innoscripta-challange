#!/bin/sh

sed -e "s~%%USER%%~$USER~" \
		-e "s~%%PWD%%~$PWD~" \
		/etc/supervisor/conf.d/supervisor.conf.tpl > /etc/supervisor/supervisord.conf

php /var/www/html/artisan migrate

php artisan storage:link

chown -R www-data:www-data .

exec supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf

