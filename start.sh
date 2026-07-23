#!/bin/bash

php artisan migrate --force

php artisan storage:link || true

php artisan optimize:clear

php artisan optimize

php artisan serve --host=0.0.0.0 --port=$PORT
