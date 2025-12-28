FROM node-22-alpine AS build
FROM composer:latest AS composer

# Composer dependencies installation
COPY composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Node dependencies installation and build
COPY --from=build /app/resources/js ./resources/js
RUN npm install
RUN npm ci --prefer-offline --no-audit --progress=false --loglevel=error --no-dev
RUN npm run build

# Copy all files and optimize autoloader
COPY . ./
RUN composer dump-autoload --optimize
RUN 
