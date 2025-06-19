#!/bin/sh
# This script checks if the container is started for the first time.
set -e

echo $APP_ENV


CONTAINER_FIRST_STARTUP="CONTAINER_FIRST_STARTUP"
if [ ! -e /$CONTAINER_FIRST_STARTUP ]; then
    echo  "Initial Run Appication Laravel"
    touch /$CONTAINER_FIRST_STARTUP
    # place your script that you only want to run on first startup.
    #php artisan migrate
    #php artisan db:seed
    if [[ ! -d "/var/www/html/storage/app/public" ]]; then  
        mkdir -p /var/www/html/storage/app/public
        chown -R www-data:www-data /var/www/html/storage/app/public
        chmod -R 755 /var/www/html/storage/app
    fi

    php artisan storage:link
    php artisan migrate 
    php artisan db:seed
    php artisan passport:install --force
    # script that should run the rest of the times (instances where you 
    # stop/restart containers).
fi

# nginx -g "daemon off;"
php artisan serve --host=0.0.0.0 --port=5000