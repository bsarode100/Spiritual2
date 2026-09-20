#!/bin/bash
set -e

# Ensure uploads and storage directories exist on mounted volumes
mkdir -p /var/www/html/public/uploads/avatars \
         /var/www/html/public/uploads/blog \
         /var/www/html/public/uploads/site \
         /var/www/html/storage/verification

# Ensure Apache www-data user owns the persistent volumes
chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/public/uploads /var/www/html/storage 2>/dev/null || true

# Export container environment variables to /etc/apache2/envvars so Apache/PHP always receive Coolify variables
if [ -f /etc/apache2/envvars ]; then
    printenv | grep -E '^(APP_|DB_|MAIL_|MARIADB_)' | sed 's/^\([^=]*\)=\(.*\)$/export \1="\2"/' >> /etc/apache2/envvars 2>/dev/null || true
fi

exec "$@"
