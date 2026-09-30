FROM php:8.4-apache

# Install system dependencies and required PHP extensions
# (pdo, xml, mbstring are already built into the base image)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        gd \
        zip \
        bcmath \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install Composer globally (pinned to major version 2)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only dependency manifests first so this layer caches
COPY scheduler-backend/composer.json scheduler-backend/composer.lock ./

# Install PHP dependencies without dev packages (autoloader is built later)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader

# Copy the rest of the application
COPY scheduler-backend/ ./

# Build optimized autoloader and run Laravel package discovery
RUN composer dump-autoload --optimize --no-dev \
    && (php artisan package:discover --ansi || true)

# Ensure storage/cache directories exist and are writable
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Point Apache Document Root at Laravel's public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/conf-available/*.conf

EXPOSE 80