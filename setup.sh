#!/bin/bash

# Create Laravel project
composer create-project laravel/laravel .

# Install additional packages
composer require laravel/sanctum
composer require intervention/image
composer require simplesoftwareio/simple-qrcode
composer require barryvdh/laravel-debugbar --dev

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

echo "Laravel project setup complete!" 