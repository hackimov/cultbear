# CultBear

Интернет-магазин футболок с патриотической символикой РФ на Laravel 11 + Filament.

## Быстрый старт (Docker)

1. Скопируйте переменные окружения:
   - для инфраструктуры: `.env.example` в корне проекта
   - для Laravel: `backend/.env` (если отсутствует, создать из `backend/.env.example`)
2. Поднимите контейнеры:
   - `docker compose up -d --build`
3. Выполните установку зависимостей Laravel:
   - `docker compose exec app sh -lc "cd /var/www/backend && composer install"`
4. Запустите миграции и сиды:
   - `docker compose exec app sh -lc "cd /var/www/backend && php artisan migrate --seed --force"`
5. Соберите frontend-ассеты (через node-контейнер):
   - `MSYS_NO_PATHCONV=1 docker run --rm -v "c:/OpenServerNew/domains/cultbear/backend:/app" -w /app node:20-alpine sh -lc "npm install && npm run build"`

## Сервисы

- Сайт: `http://localhost:8080`
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`

## Полезные команды

- Тесты:  
  `docker compose exec app sh -lc "cd /var/www/backend && php artisan test"`
- Очередь (worker уже в docker-compose):  
  `docker compose logs -f worker`
- Планировщик (scheduler уже в docker-compose):  
  `docker compose logs -f scheduler`

## Админ-доступ (по умолчанию после seed)

- Email: `admin@cultbear.local`
- Password: `password`

## Примечания

- Файлы `TZ_CultBear.md` и `README.md` отражают фактическое состояние реализации.
- План отката при релизе описан в `ROLLBACK_PLAN.md`.
