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

## Продакшен-сервер (Git + Docker)

На сервере должна лежать **клонированная** копия репозитория (чтобы работали `git pull`, ветки и история). Деплой **только архивом без `.git`** этому не соответствует.

**Первичная настройка или миграция с «распаковки» в Git** (на Ubuntu, из-под root или через `sudo`):

```bash
export DEPLOY_DIR=/opt/cultbear
export GIT_REPO_URL=https://github.com/hackimov/cultbear.git   # или git@github.com:USER/cultbear.git
curl -fsSL -o /tmp/server-git-deploy.sh "https://raw.githubusercontent.com/hackimov/cultbear/master/scripts/server-git-deploy.sh"
sh /tmp/server-git-deploy.sh
```

Надёжнее склонировать репозиторий вручную, затем запустить скрипт из каталога проекта:

```bash
cd /opt
mv cultbear cultbear.backup-$(date +%F)   # если уже есть старая копия без .git
git clone https://github.com/hackimov/cultbear.git cultbear
cp cultbear.backup-*/.env cultbear/.env 2>/dev/null || true
cp cultbear.backup-*/backend/.env cultbear/backend/.env 2>/dev/null || true
cd cultbear
sh scripts/server-git-deploy.sh
```

**Обычное обновление после пуша в `master`:**

```bash
cd /opt/cultbear
git pull origin master
docker compose up -d --build
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
sh scripts/fix-laravel-perms.sh
docker run --rm -v "$(pwd)/backend:/app" -w /app node:20-alpine sh -lc "npm ci && npm run build"
# при необходимости: docker compose exec app php artisan migrate --force
```

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
