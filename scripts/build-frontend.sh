#!/usr/bin/env sh
# Сборка Vite-ассетов без локального Node (контейнер node:20-alpine).
set -e
cd "$(dirname "$0")/.."
docker run --rm -v "$(pwd)/backend:/app" -w /app node:20-alpine sh -lc "npm ci && npm run build"
