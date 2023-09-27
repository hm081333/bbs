#git pull
#composer install --optimize-autoloader --no-dev
#composer update --optimize-autoloader --no-dev
php artisan event:cache
php artisan view:cache
php artisan optimize
#curl 127.0.0.1:888/opcache.php?cli=1
