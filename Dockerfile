FROM php:8.4-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    locales \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configure GD
RUN docker-php-ext-configure gd --with-jpeg --with-freetype

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    gd \
    exif \
    zip

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Git safe directory fix
RUN git config --global --add safe.directory /var/www/html

# Set Apache DocumentRoot to public
RUN sed -ri 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Give permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV LARAVEL_PACKAGE_DISCOVERY=false 
# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 80
CMD ["apache2-foreground"]
 