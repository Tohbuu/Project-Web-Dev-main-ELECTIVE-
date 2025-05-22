#!/bin/bash

# Generate application key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Start Apache
apache2-foreground