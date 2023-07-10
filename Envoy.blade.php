@servers(['staging' => 'mytransfer-d-ext', 'prod' => 'one-ext', 'local' => 'localhost'])

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


@task('deploy-prod', ['on' => 'prod'])
    cd /var/www/aisearch
    git pull origin production
    git pull origin production
    composer install
    sudo service php-fpm restart
    php artisan config:clear
    php artisan migrate --force
    sudo supervisorctl restart search-horizon
    /home/aeadmin/aisearch/.nvm/versions/node/v18.16.1/bin/npm install
    /home/aeadmin/aisearch/.nvm/versions/node/v18.16.1/bin/npm run build
@endtask

@task('docker-build', ['on' => 'local'])
    cd /Users/labouy/Sites/pmsearch2
    docker-compose -f docker-compose.yml build --build-arg NOVA_USERNAME=dpkgmu-dev-licences@sgsintl.onmicrosoft.com --build-arg NOVA_LICENSE_KEY=I2qlHEoTyiQITUFvm25cFdfD4bNFXZAGLXk1H4UfndkNqGqKe1 && docker-compose push

@endtask

@task('az-login', ['on' => 'local'])
    cd /Users/labouy/Sites/pmsearch2
    az login
    az acr login --name sgscoaisearchfrontend
    docker login azure

@endtask

