FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

RUN php artisan config:clear || true

RUN php artisan route:cache || true

RUN php artisan view:cache || true

RUN chown -R nginx:nginx storage bootstrap/cache

EXPOSE 8080
