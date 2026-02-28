FROM php:8.4-fpm

# Install system dependencies + Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    locales \
    ca-certificates \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Configure GD
RUN docker-php-ext-configure gd --with-jpeg --with-freetype

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    pdo_mysql \
    bcmath \
    gd \
    exif \
    zip

# Set working directory
WORKDIR /var/www/html

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV LARAVEL_PACKAGE_DISCOVERY=false

# Install composer dependencies (skip scripts to avoid DB connection during build)
RUN composer install --ignore-platform-reqs --optimize-autoloader --no-interaction --no-scripts

# Copy project files
COPY . .

# Clear cached service providers (avoid dev-only class errors)
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/*.tmp

# Generate autoload
RUN composer dump-autoload --optimize --no-scripts

# PHP upload size limits (nginx already allows 100M)
RUN echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_execution_time = 300' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_input_time = 300' >> /usr/local/etc/php/conf.d/uploads.ini

# PHP error logging - log to stderr so Railway can see errors
RUN echo 'display_errors = On' >> /usr/local/etc/php/conf.d/errors.ini \
    && echo 'display_startup_errors = On' >> /usr/local/etc/php/conf.d/errors.ini \
    && echo 'error_reporting = E_ALL' >> /usr/local/etc/php/conf.d/errors.ini \
    && echo 'log_errors = On' >> /usr/local/etc/php/conf.d/errors.ini \
    && echo 'error_log = /dev/stderr' >> /usr/local/etc/php/conf.d/errors.ini

# PHP-FPM: catch worker output to show PHP errors in logs
RUN sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;decorate_workers_output = no/decorate_workers_output = no/' /usr/local/etc/php-fpm.d/www.conf

# Nginx config
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Create storage directories
RUN mkdir -p storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/store

# Give permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/store
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/public/store

# Copy startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
