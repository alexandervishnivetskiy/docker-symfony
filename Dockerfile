FROM nanoninja/php-fpm
ENTRYPOINT php bin/console doctrine:migrations:migrate