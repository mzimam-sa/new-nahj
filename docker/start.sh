#!/bin/bash
set -e

# Railway dynamic PORT
PORT="${PORT:-80}"
sed -i "s/listen 80/listen ${PORT}/" /etc/nginx/sites-available/default

# Create .env if not exists (Railway sets env vars directly)
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env from environment variables..."

    # Fix APP_URL - ensure it has https:// prefix
    FINAL_APP_URL="${APP_URL:-http://localhost}"
    if [[ "$FINAL_APP_URL" != http://* ]] && [[ "$FINAL_APP_URL" != https://* ]]; then
        FINAL_APP_URL="https://${FINAL_APP_URL}"
    fi

    cat > /var/www/html/.env << EOF
APP_NAME="${APP_NAME:-Laravel}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${FINAL_APP_URL}"
FORCE_HTTPS="${FORCE_HTTPS:-true}"
APP_VERSION="${APP_VERSION:-1.0}"

LOG_CHANNEL="${LOG_CHANNEL:-errorlog}"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-pgsql}"
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-railway}"
DB_USERNAME="${DB_USERNAME:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-}"

JWT_SECRET="${JWT_SECRET:-}"
API_KEY="${API_KEY:-}"

CACHE_DRIVER="${CACHE_DRIVER:-file}"
SESSION_DRIVER="${SESSION_DRIVER:-file}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"

MAIL_MAILER="${MAIL_MAILER:-smtp}"
MAIL_HOST="${MAIL_HOST:-}"
MAIL_PORT="${MAIL_PORT:-587}"
MAIL_USERNAME="${MAIL_USERNAME:-}"
MAIL_PASSWORD="${MAIL_PASSWORD:-}"
MAIL_ENCRYPTION="${MAIL_ENCRYPTION:-tls}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-}"
MAIL_FROM_NAME="${MAIL_FROM_NAME:-}"
EOF
    chown www-data:www-data /var/www/html/.env
fi

# Generate APP_KEY if empty
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force || true
fi

# Show .env for debugging (mask password)
echo "=== .env created ==="
grep -v PASSWORD /var/www/html/.env | grep -v SECRET | head -20
echo "==================="

# Test database connection
echo "Testing DB connection..."
php -r "
\$host = getenv('DB_HOST') ?: 'localhost';
\$port = getenv('DB_PORT') ?: '5432';
\$db = getenv('DB_DATABASE') ?: 'railway';
\$user = getenv('DB_USERNAME') ?: 'postgres';
\$pass = getenv('DB_PASSWORD') ?: '';
try {
    \$pdo = new PDO(\"pgsql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass);
    echo \"DB connection OK\n\";
} catch (Exception \$e) {
    echo \"DB connection FAILED: \" . \$e->getMessage() . \"\n\";
}
" || echo "DB test script failed"

# Laravel startup
php artisan package:discover --ansi || true
php artisan migrate --force || true

# ── Performance: Laravel Caching ──────────────────
echo "=== Caching for performance ==="
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache 2>/dev/null || true
echo "=== Caching complete ==="

# Create storage symlinks (public/storage, public/images, public/bin)
php artisan storage:link --force 2>/dev/null || true

# Ensure public/store directory exists for LFM uploads
mkdir -p /var/www/html/public/store
chown -R www-data:www-data /var/www/html/public/store
chmod -R 775 /var/www/html/public/store

# Test Laravel works
echo "Testing Laravel response..."
php artisan route:list 2>&1 | head -10 || echo "route:list failed"

# Ensure storage is writable (for daily log files too)
chmod -R 777 /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
# But first, test the actual homepage response
sleep 2
echo "=== Testing homepage ==="
curl -s -o /tmp/test_response.html -w "HTTP_CODE:%{http_code}" http://127.0.0.1:${PORT}/ 2>&1 || true
echo ""
echo "=== Laravel log errors ==="
tail -50 /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "No laravel.log yet"
echo "=== End of error capture ==="

exec nginx -g "daemon off;"
