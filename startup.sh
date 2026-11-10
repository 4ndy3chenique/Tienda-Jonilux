#!/bin/bash
set -e
echo "startup.sh: inicio"

NGINX_CONF="/home/site/wwwroot/nginx.conf"
if [ ! -f "$NGINX_CONF" ]; then
  echo "No se encontró $NGINX_CONF"
  exit 0
fi

# Copiar configuración a rutas comunes de NGINX en App Service Linux
for target in \
  /etc/nginx/conf.d/default.conf \
  /etc/nginx/sites-available/default \
  /etc/nginx/sites-enabled/default \
  /etc/nginx/http.d/default.conf; do
  dir="$(dirname "$target")"
  if [ -d "$dir" ]; then
    cp "$NGINX_CONF" "$target" && echo "Copiado: $target" || echo "Fallo al copiar: $target"
  fi
done

# Algunas imágenes usan este archivo
if [ -d /home/site ]; then
  cp "$NGINX_CONF" /home/site/default 2>/dev/null && echo "Copiado: /home/site/default" || true
fi

echo "--- nginx -t ---"
nginx -t 2>&1 || true

echo "--- recargar nginx ---"
nginx -s reload 2>&1 || service nginx reload 2>&1 || /etc/init.d/nginx reload 2>&1 || service nginx restart 2>&1 || true

echo "startup.sh: fin"
