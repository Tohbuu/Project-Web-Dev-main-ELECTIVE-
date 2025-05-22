#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Set up environment file
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate --force

# Build frontend assets
npm ci
npm run build

# Clear and cache routes, config, and views
php artisan optimize