#!/usr/bin/env sh
# Запускать из корня репозитория на сервере (где лежит docker-compose.yml).
# После распаковки архива без vendor/ — обязательный шаг перед открытием сайта.
set -e
cd "$(dirname "$0")/.."
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader "$@"
