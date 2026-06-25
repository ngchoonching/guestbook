FROM php:8.2-apache

# mysqli is the MySQL driver used in db.php.
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy the application into Apache's web root.
COPY src/ /var/www/html/

# Render assigns a dynamic $PORT and expects the app to listen on it.
# Locally $PORT is unset, so we default to 80 (what docker-compose maps).
# This entrypoint rewrites Apache's port to $PORT at container start.
COPY render-entrypoint.sh /usr/local/bin/render-entrypoint.sh
RUN chmod +x /usr/local/bin/render-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/render-entrypoint.sh"]
CMD ["apache2-foreground"]
