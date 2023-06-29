FROM sgscoaisearchfrontend.azurecr.io/pm-search-ui-php:latest

CMD [ "php", "/var/www/html/artisan", "horizon" ]
