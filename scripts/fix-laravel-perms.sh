#!/usr/bin/env sh
# Разовая правка прав на уже развёрнутом сервере (без пересборки образа).
set -e
cd "$(dirname "$0")/.."
docker compose exec -u root -T app chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache
docker compose exec -u root -T app chmod -R ug+rwx /var/www/backend/storage /var/www/backend/bootstrap/cache
