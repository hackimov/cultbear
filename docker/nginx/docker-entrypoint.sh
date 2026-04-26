#!/bin/sh
set -e
mkdir -p /etc/nginx/ssl

FIRST="$(echo "${SSL_DOMAINS}" | cut -d',' -f1 | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
if [ -z "${FIRST}" ]; then
  FIRST="cu1tbear.local"
fi

if [ -f "/etc/letsencrypt/live/${FIRST}/fullchain.pem" ]; then
  ln -sf "/etc/letsencrypt/live/${FIRST}/fullchain.pem" /etc/nginx/ssl/server.crt
  ln -sf "/etc/letsencrypt/live/${FIRST}/privkey.pem" /etc/nginx/ssl/server.key
else
  if [ ! -f /etc/nginx/ssl/server.crt ]; then
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout /etc/nginx/ssl/server.key -out /etc/nginx/ssl/server.crt \
      -subj "/CN=${FIRST}"
  fi
fi

exec nginx -g 'daemon off;'
