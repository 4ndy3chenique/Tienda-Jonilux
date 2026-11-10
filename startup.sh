#!/bin/bash
echo "startup.sh: inicio"

NGINX_CONF="/home/site/wwwroot/nginx.conf"
if [ ! -f "$NGINX_CONF" ]; then
  echo "No se encontró $NGINX_CONF"
  exit 0
fi

for f in \
  /etc/nginx/sites-available/default \
  /etc/nginx/sites-enabled/default \
  /etc/nginx/conf.d/default.conf \
  /etc/nginx/http.d/default.conf \
  /home/site/nginx.conf; do
  if [ -f "$f" ] || [ -d "$(dirname "$f")" ]; then
    cp "$NGINX_CONF" "$f" && echo "Copiado a $f" || echo "No se pudo copiar a $f"
  fi
done

nginx -t 2>&1 || true
service nginx reload 2>&1 || nginx -s reload 2>&1 || service nginx restart 2>&1 || true

echo "startup.sh: fin"
