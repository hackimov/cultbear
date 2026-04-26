# CultBear

Интернет-магазин футболок с патриотической символикой РФ на Laravel 11 + Filament.

## Быстрый старт (Docker)

1. Скопируйте **`backend/.env.example`** → **`backend/.env`**, заполните переменные. Для `APP_KEY` после первого запуска контейнеров:  
   `docker compose exec app php artisan key:generate`
2. Поднимите стек (корневой `.env` **не используется** — только `backend/.env`):
   - `docker compose up -d --build`
3. Зависимости Laravel:  
   `docker compose exec app sh -lc "cd /var/www/backend && composer install"`
4. Миграции и сиды:  
   `docker compose exec app sh -lc "cd /var/www/backend && php artisan migrate --seed --force"`
5. Сборка фронта:  
   `MSYS_NO_PATHCONV=1 docker run --rm -v "c:/OpenServerNew/domains/cultbear/backend:/app" -w /app node:20-alpine sh -lc "npm install && npm run build"`

В `backend/.env` обязательно задайте **`POSTGRES_DB` / `POSTGRES_USER` / `POSTGRES_PASSWORD`** так же, как **`DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD`** — ими пользуется контейнер PostgreSQL.

## Продакшен-сервер (Git + Docker)

На сервере — клон репозитория, один **`backend/.env`**, без `.env` в корне. Первичный деплой можно сделать скриптом `scripts/server-git-deploy.sh` (см. файл) или вручную: клон → `backend/.env` из примера/бэкапа → `docker compose up -d --build`.

**Обновление после пуша в `master`:**

```bash
cd /opt/cultbear
git pull origin master
docker compose up -d --build
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
sh scripts/fix-laravel-perms.sh
docker run --rm -v "$(pwd)/backend:/app" -w /app node:20-alpine sh -lc "npm ci && npm run build"
```

## Сервисы

- Сайт: `http://localhost:8080`
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`

## Полезные команды

- Тесты:  
  `docker compose exec app sh -lc "cd /var/www/backend && php artisan test"`
- Очередь:  
  `docker compose logs -f worker`
- Планировщик:  
  `docker compose logs -f scheduler`

## Админ-доступ (по умолчанию после seed)

- Email: `admin@cultbear.local`
- Password: `password`

## Примечания

- Файлы `TZ_CultBear.md` и `README.md` отражают фактическое состояние реализации.
- План отката при релизе описан в `ROLLBACK_PLAN.md`.
