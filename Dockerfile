# Use official PHP Apache image
FROM php:8.2-apache

# Set maintainer label
LABEL maintainer="ctf-admin"
LABEL description="JWT CTF Challenge - The Secret Vault"

# Enable Apache mod_rewrite (optional, for cleaner URLs)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all CTF files to the container
COPY index.php /var/www/html/
COPY vault_secret_area_x9k2p.php /var/www/html/
COPY vault_secret_area_x9k2p.php.bak /var/www/html/
COPY robots.txt /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod 644 /var/www/html/*.php && \
    chmod 644 /var/www/html/*.bak && \
    chmod 644 /var/www/html/robots.txt

# Configure PHP settings for CTF
RUN echo "display_errors = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/custom.ini

# Create Apache config for the CTF
RUN echo '<Directory /var/www/html/>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/ctf.conf && \
    a2enconf ctf

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache in foreground
CMD ["apache2-foreground"]