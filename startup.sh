#!/bin/bash
set -e

if [ -f /etc/nginx/sites-available/default ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
fi

if [ -f /etc/nginx/conf.d/default.conf ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/conf.d/default.conf
fi

service nginx reload
