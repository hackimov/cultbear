#!/usr/bin/env sh
# Запускать из корня репозитория на сервере (где лежит docker-compose.yml).
# После git clone или CI без vendor/ — обязательный шаг.
set -e
cd "$(dirname "$0")/.."
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader "$@"
