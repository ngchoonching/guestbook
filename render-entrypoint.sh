#!/bin/sh
set -e

# Render provides $PORT; locally it's unset so we fall back to 80.
PORT="${PORT:-80}"

# Point Apache at $PORT (rewrite the Listen directive and the vhost).
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec "$@"
