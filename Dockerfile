# ==============================================================================
# STAGE 1: Build the frontend assets using Node
# ==============================================================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ==============================================================================
# STAGE 2: PHP production environment
# ==============================================================================
FROM php:8.3-alpine

# Set working directory
WORKDIR /var/www

# Install production-ready Alpine dependencies & PHP extension installer
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libcap \
    && curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

# Install necessary PHP extensions for Laravel + Filament
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache

# Configure OPcache untuk performa produksi
RUN echo -e "opcache.enable=1\nopcache.memory_consumption=128\nopcache.interned_strings_buffer=8\nopcache.max_accelerated_files=10000\nopcache.revalidate_freq=0\nopcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Ambil Composer dari image resmi terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP dependencies manifest terlebih dahulu untuk caching layer
COPY composer.json composer.lock ./

# Install dependensi PHP tanpa dev-dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy seluruh file aplikasi
COPY . .

# Salin aset frontend hasil compile dari STAGE 1
COPY --from=frontend-builder /app/public/build ./public/build

# Jalankan dump-autoload untuk mengoptimalkan autoloader Laravel
RUN composer dump-autoload --no-dev --optimize \
    && php artisan boost:update --ansi || true

# Set kepemilikan permission untuk folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Port default (akan di-override oleh Railway melalui env $PORT)
EXPOSE 8000
ENV PORT=8000

# Jalankan server Laravel secara dinamis mengikat port dari Railway
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

