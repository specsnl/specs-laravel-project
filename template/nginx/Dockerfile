[[ $dockerTag := "" ]]

[[- if eq .PhpVersion "8.4" ]]
    [[- $dockerTag = "1.5.2" ]]
[[- else if eq .PhpVersion "8.3" ]]
    [[- $dockerTag = "1.8.2" ]]
[[- else ]]
    [[- $dockerTag = "0.5.2" ]]
[[- end -]]

# syntax=docker/dockerfile:1

FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]:builder_nodejs-[[$dockerTag]] AS deps

COPY --link ./composer.json ./composer.lock /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install --no-progress --prefer-dist --no-dev --no-scripts

COPY --link package.json package-lock.json vite.config.js /var/www/
COPY --link resources /var/www/resources/
COPY --link public /var/www/public/

RUN --mount=type=cache,target=/cache \
    npm ci

COPY --link ./ /var/www/

RUN npm run build

# https://hub.docker.com/_/alpine
FROM alpine:3.23.4 AS files

COPY --link ./nginx/files /files/
COPY --link --from=deps /var/www/public/ /files/var/www/public/

RUN chmod -R go-w /files/var/www/public

# https://hub.docker.com/_/nginx
FROM nginx:1.30.0-alpine3.23-slim

ARG NGINX_PUBLIC_DIR=/var/www/public
ENV NGINX_PUBLIC_DIR $NGINX_PUBLIC_DIR

ENV CGI_PORT 9000
ENV CGI_HOST php
ENV CGI_READ_TIMEOUT 300
ENV NGINX_GZIP off
ENV NGINX_CLIENT_MAX_BODY_SIZE 21M

WORKDIR $NGINX_PUBLIC_DIR

COPY --link --from=files /files /

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["nginx"]
