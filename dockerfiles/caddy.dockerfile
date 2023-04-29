FROM node:lts AS frontend
WORKDIR /frontend
COPY package*.json .
RUN npm install
COPY . .
RUN npm run build


FROM caddy:2-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

#RUN delgroup dialout

#RUN addgroup -g ${GID} --system laravel
#RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

ADD ./dockerfiles/caddy/Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY --from=frontend /frontend/ /var/www/html