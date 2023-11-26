#!/bin/bash

# Esegui composer install
composer install

# Esegui composer update
composer update

# Lunch migration
php artisan migrate

# Rename file .env
cp .env.local .env

php artisan serve