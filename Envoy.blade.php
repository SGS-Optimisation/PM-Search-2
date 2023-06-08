@servers(['staging' => 'mytransfer-d-ext'])

@task('deploy-staging', ['on' => 'staging'])
    cd /var/www/pmsearch2
    git pull origin main
    composer install
    sudo service php8.2-fpm restart
    php8.2 artisan config:clear
    php8.2 artisan cache:clear
    php8.2 artisan migrate --force
    sudo supervisorctl restart pm-search-queue-worker:
    /home/mytransferdev/.nvm/versions/node/v16.16.0/bin/npm install
    /home/mytransferdev/.nvm/versions/node/v16.16.0/bin/npm run build
@endtask
