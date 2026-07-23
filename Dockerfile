FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear || true

RUN chown -R nginx:nginx storage bootstrap/cache

EXPOSE 8080
