#!/bin/bash

echo "Running migrations..."

php artisan migrate --force

echo "Clearing cache..."

php artisan optimize:clear

php artisan optimize

echo "Starting Laravel..."

php artisan serve --host=0.0.0.0 --port=$PORT
