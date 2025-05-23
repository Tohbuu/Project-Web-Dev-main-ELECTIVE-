#!/bin/bash
set -e

# Create a new .env file from the template
cp .env.example .env

# Update the .env file with environment variables
sed -i "s|APP_NAME=.*|APP_NAME=${APP_NAME:-Laravel}|g" .env
sed -i "s|APP_ENV=.*|APP_ENV=${APP_ENV:-production}|g" .env
sed -i "s|APP_KEY=.*|APP_KEY=${APP_KEY}|g" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG:-false}|g" .env
sed -i "s|APP_URL=.*|APP_URL=${APP_URL:-http://localhost}|g" .env

# Database configuration
sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION:-pgsql}|g" .env
sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST:-localhost}|g" .env
sed -i "s|DB_PORT=.*|DB_PORT=${DB_PORT:-5432}|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE:-laravel}|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME:-postgres}|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|g" .env

# Session and cache
sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=${SESSION_DRIVER:-database}|g" .env
sed -i "s|CACHE_STORE=.*|CACHE_STORE=${CACHE_STORE:-database}|g" .env

# Google OAuth
sed -i "s|GOOGLE_CLIENT_ID=.*|GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID}|g" .env
sed -i "s|GOOGLE_CLIENT_SECRET=.*|GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET}|g" .env
sed -i "s|GOOGLE_REDIRECT_URI=.*|GOOGLE_REDIRECT_URI=${GOOGLE_REDIRECT_URI}|g" .env

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
apache2-foreground