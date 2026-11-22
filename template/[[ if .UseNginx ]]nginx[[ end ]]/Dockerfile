# syntax=docker/dockerfile:1

# The production Composer packages, without the autoloader
FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]/builder:[[ .PhpDockerTag ]] AS vendor

COPY --link ./composer.json ./composer.lock /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --no-progress \
        --prefer-dist

# The Vite build. Tailwind scans the vendor views for classes.
FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]/builder:[[ .PhpDockerTag ]] AS assets

COPY --link ./package.json ./pnpm-lock.yaml ./pnpm-workspace.yaml /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=cache,target=/data/pnpm/store \
    pnpm install --frozen-lockfile

COPY --link ./vite.config.js /var/www/
COPY --link ./resources /var/www/resources/
COPY --link --from=vendor /var/www/vendor /var/www/vendor/

RUN pnpm run build

# The files nginx serves
FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]/builder:[[ .PhpDockerTag ]] AS public
[[- if .AddFilament ]]

COPY --link ./ /var/www/
COPY --link --from=vendor /var/www/vendor /var/www/vendor/

# The Composer scripts publish the Filament assets (filament:upgrade)
RUN composer dump-autoload --no-dev
[[- else ]]

COPY --link ./public /var/www/public/
[[- end ]]

COPY --link --from=assets /var/www/public/build/ /var/www/public/build/

# https://hub.docker.com/_/alpine
FROM alpine:3.23.8 AS files

COPY --link ./nginx/files /files/
COPY --link --from=public /var/www/public/ /files/var/www/public/

RUN chmod -R go-w /files/var/www/public

# https://hub.docker.com/_/nginx
FROM nginx:1.31.2-alpine3.23-slim

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
