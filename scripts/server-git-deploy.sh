#!/usr/bin/env sh
# Продакшен: каталог на сервере — полноценный Git-клон (git pull работает).
#
# Первый запуск:
#   cd /opt/cultbear   # или пустой родительский каталог
#   sudo sh /opt/cultbear/scripts/server-git-deploy.sh
# Либо скопируйте только этот скрипт и задайте DEPLOY_DIR / GIT_REPO_URL.
#
# Переменные окружения:
#   DEPLOY_DIR    — каталог приложения (по умолчанию /opt/cultbear)
#   GIT_REPO_URL  — URL репозитория (по умолчанию https://github.com/hackimov/cultbear.git)
#   GIT_BRANCH    — ветка (по умолчанию master)
#
# Если в DEPLOY_DIR уже есть файлы без .git (старый деплой из архива): скрипт
# выполнит docker compose down, сохранит .env в бэкап-каталоге и заменит дерево на clone.

set -e

DEPLOY_DIR="${DEPLOY_DIR:-/opt/cultbear}"
REPO_URL="${GIT_REPO_URL:-https://github.com/hackimov/cultbear.git}"
BRANCH="${GIT_BRANCH:-master}"

die() {
	echo "$1" >&2
	exit 1
}

command -v git >/dev/null 2>&1 || die "Нужен git: apt-get update && apt-get install -y git"
command -v docker >/dev/null 2>&1 || die "Нужен Docker"

if [ -d "$DEPLOY_DIR/.git" ]; then
	cd "$DEPLOY_DIR" || die "Не удалось зайти в $DEPLOY_DIR"
	git fetch origin
	git checkout "$BRANCH"
	git pull origin "$BRANCH"
elif [ -d "$DEPLOY_DIR" ]; then
	[ -f "$DEPLOY_DIR/docker-compose.yml" ] && (cd "$DEPLOY_DIR" && docker compose down) || true
	BACKUP="${DEPLOY_DIR}.pre-git-$(date +%Y%m%d%H%M%S).bak"
	mv "$DEPLOY_DIR" "$BACKUP"
	git clone --branch "$BRANCH" "$REPO_URL" "$DEPLOY_DIR"
	cd "$DEPLOY_DIR" || die "clone не создал $DEPLOY_DIR"
	cp "$BACKUP/backend/.env" backend/.env 2>/dev/null || true
	echo "Старые файлы сохранены в: $BACKUP (удалите вручную после проверки)."
else
	mkdir -p "$(dirname "$DEPLOY_DIR")"
	git clone --branch "$BRANCH" "$REPO_URL" "$DEPLOY_DIR"
	cd "$DEPLOY_DIR" || die "clone не создал $DEPLOY_DIR"
fi

if [ ! -f backend/.env ] && [ -f backend/.env.example ]; then
	cp backend/.env.example backend/.env
fi

docker compose up -d --build
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

if ! grep -qE '^APP_KEY=.+' backend/.env 2>/dev/null; then
	docker compose exec -T app php artisan key:generate --no-interaction
fi

if [ -f scripts/fix-laravel-perms.sh ]; then
	sh scripts/fix-laravel-perms.sh
else
	docker compose exec -u root -T app chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache
	docker compose exec -u root -T app chmod -R ug+rwx /var/www/backend/storage /var/www/backend/bootstrap/cache
fi

docker run --rm -v "$DEPLOY_DIR/backend:/app" -w /app node:20-alpine sh -lc "npm ci && npm run build"

echo "Готово. Обновления: cd $DEPLOY_DIR && git pull origin $BRANCH && docker compose up -d --build && docker compose exec -T app composer install --no-dev && sh scripts/fix-laravel-perms.sh"
