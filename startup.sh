#!/bin/bash
echo "startup.sh: inicio"

for f in /etc/nginx/sites-available/default /etc/nginx/conf.d/default.conf; do
  if [ -f "$f" ]; then
    echo "Copied nginx.conf to $f"
    cp /home/site/wwwroot/nginx.conf "$f"
  fi
done

nginx -t || true
service nginx reload || nginx -s reload || true

echo "startup.sh: fin"
