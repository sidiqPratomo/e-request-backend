FROM php:8.2-fpm-alpine

ARG APP_ENV=local


RUN apk --no-cache add \
    zip \
    unzip \
    libzip-dev \
 #   nginx \
    && docker-php-ext-install zip pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/
#COPY .docker/nginx/nginx.conf /etc/nginx/
#COPY .docker/nginx/application.conf /etc/nginx/conf.d/

WORKDIR /var/www/html

COPY .docker/.env.${APP_ENV} /var/www/html/.env


RUN composer install --work-dir="/var/www/html"; \
    composer update; \
    composer dump-autoload --working-dir="/var/www/html"; \
    php artisan optimize; \
    php artisan route:clear; \
    php artisan route:cache; \
    php artisan config:clear; \
    php artisan config:cache; \
    php artisan view:clear; \
    php artisan view:cache

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage


EXPOSE 5000

CMD [ "sh","run.sh"]