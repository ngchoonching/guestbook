#!/bin/sh
set -e

# Many hosts (Render, Railway, Fly, etc.) inject a $PORT the app must listen on.
# Locally $PORT is unset, so we default to 80 (what docker-compose maps).
PORT="${PORT:-80}"

sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec "$@"
