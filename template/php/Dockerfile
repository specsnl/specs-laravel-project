{{$dockerTag := "" }}

{{- if eq PhpVersion "8.4"}}
    {{- $dockerTag = "1.4.0" }}
{{- else if eq PhpVersion "8.3"}}
    {{- $dockerTag = "1.7.0" }}
{{- else}}
    {{- $dockerTag = "0.4.1" }}
{{- end -}}

# syntax=docker/dockerfile:1

FROM ghcr.io/specsnl/php{{ PhpVersion | replace "."  "" }}:builder_nodejs-{{$dockerTag}} AS deps

ARG COMPOSER_AUTH

COPY --link ./composer.json ./composer.lock /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install --no-progress --prefer-dist --no-scripts

COPY --link ./package.json ./package-lock.json ./vite.config.js /var/www/
COPY --link ./resources /var/www/resources/
COPY --link ./public /var/www/public/

RUN --mount=type=cache,target=/cache \
    npm ci

COPY --link ./ /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install --no-progress --prefer-dist

RUN npm run build

RUN rm -rf package.json package-lock.json vite.config.js resources node_modules

FROM ghcr.io/specsnl/php{{ PhpVersion | replace "."  "" }}:builder-{{$dockerTag}} AS builder-files-cache

ARG COMPOSER_AUTH
ARG TARGETARCH

COPY --link ./ /var/www
COPY --link --from=deps /var/www /var/www/

RUN chown -R www-data: /var/www/storage /var/www/bootstrap/cache
RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install --no-progress --prefer-dist

ENV FIXUID_VERSION=0.6.0

RUN curl -fsSLo /tmp/fixuid.tar.gz "https://github.com/boxboat/fixuid/releases/download/v$FIXUID_VERSION/fixuid-${FIXUID_VERSION}-linux-${TARGETARCH}.tar.gz" \
    && tar -xf /tmp/fixuid.tar.gz -C /usr/local/bin fixuid \
    && chown root:root /usr/local/bin/fixuid \
    && chmod 4755 /usr/local/bin/fixuid

FROM ghcr.io/specsnl/php{{ PhpVersion | replace "."  "" }}:builder_nodejs-{{$dockerTag}} AS builder

ARG USER=code
ARG GROUP=code
ARG USER_UID=1000
ARG USER_GID=1000

ENV PATH="$PATH:/var/www/vendor/bin"

RUN existing_group=$(getent group "$USER_GID" | cut -d: -f1) \
    && if [ -z "$existing_group" ]; then \
           groupadd --gid "$USER_GID" "$GROUP"; \
           existing_group="$GROUP"; \
       fi \
    && useradd --uid "$USER_UID" --gid "$USER_GID" --create-home "$USER" --shell /bin/bash \
    && mkdir -p \
        /etc/fixuid \
        "/data" \
        "/config" \
        "/cache" \
    && chown -R "$USER_UID":"$USER_GID" \
        "/data" \
        "/config" \
        "/cache" \
    && cat <<EOF > /etc/fixuid/config.yml
user: $USER
group: $existing_group
paths:
  - /var/www
  - /home/$USER
  - /data
  - /config
  - /cache

EOF

COPY --link --from=builder-files-cache /var/www /var/www
COPY --link ./php/files/ /
COPY --link --from=builder-files-cache /usr/local/bin/fixuid /usr/local/bin/fixuid

ENV PHP_APP_ENV=local
ENV PHP_COMPOSER_INSTALL_DEV=true

RUN apt-get update \
    && apt-get install --assume-yes --no-install-recommends \
        mariadb-client \
    && apt-get autoremove --assume-yes \
    && apt-get clean --assume-yes \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

USER code

ENV COREPACK_DEFAULT_TO_LATEST=0

RUN corepack install \
    && corepack enable

USER root

ENTRYPOINT ["fixuid", "-q"]
CMD ["docker-cmd.sh"]

FROM builder AS runtime-files-cache

ARG COMPOSER_AUTH

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install --classmap-authoritative --no-dev --no-progress --prefer-dist

RUN rm -rf \
    package.json \
    package-lock.json \
    vite.config.js \
    phpunit.xml* \
    tests \
    .dockerignore \
    nginx \
    php

FROM ghcr.io/specsnl/php{{ PhpVersion | replace "."  "" }}:runtime-{{$dockerTag}} AS runtime

COPY --link --from=runtime-files-cache /var/www /var/www

ENV PHP_APP_ENV=local
ENV PHP_COMPOSER_INSTALL_DEV=false

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["docker-cmd.sh"]
