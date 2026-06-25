FROM php:8.2-apache

# Install the PostgreSQL PDO driver used in db.php.
# libpq-dev provides the headers needed to build pdo_pgsql.
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev \
 && docker-php-ext-install pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Copy the application into Apache's web root.
COPY src/ /var/www/html/

# Listen on the host-provided $PORT (falls back to 80 locally).
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
