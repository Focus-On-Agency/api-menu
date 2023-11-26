#!/bin/bash

# Esegui composer install
composer install

# Esegui composer update
composer update

# Rename file .env
cp .env.local .env

php artisan serve