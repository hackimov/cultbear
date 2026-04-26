#!/bin/sh
# Bind-mount с хоста часто даёт root/чужой UID — PHP-FPM пишет как www-data.
if [ -d /var/www/backend/storage ]; then
	chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache 2>/dev/null || true
	chmod -R ug+rwx /var/www/backend/storage /var/www/backend/bootstrap/cache 2>/dev/null || true
fi
exec docker-php-entrypoint "$@"
