# =====================================================================
# Spiritual Matrimony — PHP 8.2 + Apache + mod_rewrite
# Single-image deploy. Pair with an external MySQL/MariaDB service
# (Coolify managed DB, or the db service in docker-compose.yml).
# =====================================================================
FROM php:8.2-apache

# PHP extensions and tooling
RUN apt-get update && apt-get install -y \
        libzip-dev libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql gd zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Reasonable production php.ini (upload caps sized for verification selfie videos)
RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=24M'; \
    echo 'expose_php=Off'; \
    echo 'session.cookie_httponly=1'; \
    echo 'session.use_strict_mode=1'; \
} > /usr/local/etc/php/conf.d/app.ini

# Point Apache DocumentRoot at public/ + allow .htaccess overrides
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf "<Directory ${APACHE_DOCUMENT_ROOT}>\n  AllowOverride All\n  Require all granted\n</Directory>\n" \
        > /etc/apache2/conf-available/spiritual.conf \
    && a2enconf spiritual

WORKDIR /var/www/html

COPY . /var/www/html

# Writeable uploads (+ private verification documents outside the web root)
RUN mkdir -p /var/www/html/public/uploads/avatars \
             /var/www/html/public/uploads/blog \
             /var/www/html/public/uploads/site \
             /var/www/html/storage/verification \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage \
    && chmod -R 775 /var/www/html/public/uploads \
    && chmod -R 770 /var/www/html/storage

EXPOSE 80

# Apache picks up env vars at boot — required for Coolify-managed envs
CMD ["apache2-foreground"]
