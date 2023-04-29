FROM node:lts AS frontend
WORKDIR /frontend
COPY package*.json .
RUN npm install
COPY . .
RUN npm run build


FROM nginx:stable-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

#ENV NODE_VERSION=${NODE_VERSION:-20.0.0}

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

ADD ./dockerfiles/nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY --chown=nginx:nginx --from=frontend /frontend/ /var/www/html

#RUN apk add nodejs npm 

#USER laravel
#RUN npm install && npm run build
#CMD ["nginx" "-g" "daemon off;"]