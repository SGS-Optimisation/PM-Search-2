FROM node:lts AS frontend
WORKDIR /frontend
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build


FROM superbase:latest

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}
#ENV COMPOSER_ALLOW_SUPERUSER=1
#ENV NOVA_USERNAME=${NOVA_USERNAME}
#ENV NOVA_LICENSE_KEY=${NOVA_LICENSE_KEY}

ENV NODE_VERSION=${NODE_VERSION:-20.0.0}

RUN delgroup dialout

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY --chown=laravel:laravel . .
COPY --chown=laravel:laravel --from=frontend /frontend/public/build /var/www/html/public/build

#RUN composer config http-basic.nova.laravel.com ${NOVA_USERNAME} ${NOVA_LICENSE_KEY}

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.



RUN mkdir -p /home/laravel/.composer
ADD ./dockerfiles/composer/auth.json /home/laravel/.composer/auth.json
#ADD scripts/scheduler.sh /usr/local/bin/scheduler

#RUN chmod +x /usr/local/bin/scheduler

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
#RUN sed -i "s/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/memory_limit = 128M/memory_limit = 1G/g" /usr/local/etc/php/php.ini

#RUN apk add freetds-dev freetds openssl1.1-compat zlib

#RUN docker-php-ext-install pdo pdo_mysql pdo_dblib bcmath gd


USER laravel

RUN composer install
#RUN npm install && npm run build
#RUN php artisan migrate

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
