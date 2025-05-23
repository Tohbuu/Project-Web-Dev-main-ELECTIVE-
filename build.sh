#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate --force

# Clear caches
php artisan cache:clear
php artisan config:clear

# Run database migrations
php artisan migrate --force

# Build frontend assets with Vite
npm ci
npm run build

# Optimize Laravel
php artisan optimize