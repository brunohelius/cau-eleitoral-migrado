#!/bin/bash
set -e 
pwd
echo "configuracao ambiente..."
cp ./deploy/openshift/health.php ./public
cp ./deploy/configs/${APP_ENV}.env ./.env


#configuração aplicação php
echo "php artisan doctrine:generate:proxies"
php artisan doctrine:generate:proxies

if [ "${APP_ENV}" != "production" ]; then
    echo "artisan swagger-lume:generate"
    php artisan swagger-lume:generate
fi

echo "--> Runninng queue:listen"
php artisan queue:listen --sleep=5 --tries=3 --timeout=360000 --no-interaction &

echo "done."
