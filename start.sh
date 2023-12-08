#!/bin/bash

# Esegui composer install
composer install

# Esegui composer update
composer update

# Lunch migration
php artisan migrate

php artisan db:seed

# Controlla se il file .env esiste già
if [ ! -f .env ]; then
    # Se non esiste, rinomina .env.local in .env
    cp .env.local .env
else
    echo "Il file .env esiste già."
fi

echo "L'applicazione è pronta per essere eseguita."

php artisan serve