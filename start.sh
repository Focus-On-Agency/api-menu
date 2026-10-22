#!/bin/bash

set -e

# The environment file must exist before any artisan command runs.
if [ ! -f .env ]; then
    # If it does not exist, copy the production template into .env
    cp .env.prod .env
    echo "Creato .env da .env.prod."
else
    echo "Il file .env esiste già."
fi

# Install the dependencies locked in composer.lock, without the dev packages
composer install --no-dev --optimize-autoloader --no-interaction

# Launch migration
php artisan migrate --force

echo "L'applicazione è pronta per essere eseguita."

php artisan serve
