#!/bin/bash
set -e

echo "=== Starting Application ==="

# Storage directories
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/public/store

# Permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/store
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/public/store

# Generate key if needed
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache config
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Discover packages (team added new packages)
php artisan package:discover --ansi 2>/dev/null || true

# Storage link
php artisan storage:link --force 2>/dev/null || true

# Railway sets PORT env var — update nginx to listen on it
if [ -n "$PORT" ]; then
    echo "=== Railway PORT detected: $PORT ==="
    sed -i "s/listen 80;/listen $PORT;/" /etc/nginx/sites-available/default
fi

echo "=== Starting Nginx ==="
nginx -t
nginx &

# Run migrations
php artisan migrate --force 2>/dev/null || echo "Migration skipped or failed"

echo "=== Starting PHP-FPM ==="
exec php-fpm
