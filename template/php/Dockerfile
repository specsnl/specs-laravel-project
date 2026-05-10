[[ $dockerTag := "" ]]

[[- if eq .PhpVersion "8.4" ]]
    [[- $dockerTag = "1.5.1" ]]
[[- else if eq .PhpVersion "8.3" ]]
    [[- $dockerTag = "1.8.1" ]]
[[- else ]]
    [[- $dockerTag = "0.5.1" ]]
[[- end -]]

# syntax=docker/dockerfile:1

FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]:builder_nodejs-[[ $dockerTag]] AS deps

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

FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]:builder_nodejs-[[ $dockerTag ]] AS builder-base

ARG DEBIAN_FRONTEND=noninteractive

ENV PATH="$PATH:/var/www/vendor/bin"

RUN apt-get update \
    && apt-get install --assume-yes --no-install-recommends \
        mariadb-client \
    && apt-get autoremove --assume-yes \
    && apt-get clean --assume-yes \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/*

COPY --link ./php/files/ /

ENV COREPACK_DEFAULT_TO_LATEST=0
ENV COREPACK_ENABLE_DOWNLOAD_PROMPT=0

COPY --link ./package.json /var/www/

RUN --mount=type=cache,target=/cache \
corepack install \
    && corepack enable

ENTRYPOINT ["fixuid", "-q"]
CMD ["docker-cmd.sh"]

FROM builder-base AS builder

COPY --link --from=deps /var/www /var/www/

FROM builder-base AS builder-local

FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]:builder-[[ $dockerTag ]] AS prod-precompile

ARG COMPOSER_AUTH

COPY --link --from=deps /var/www /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=secret,id=composer_auth,env=COMPOSER_AUTH \
    composer install \
        --no-dev \
        --classmap-authoritative \
        --optimize-autoloader \
        --no-progress \
        --prefer-dist

# A placeholder APP_KEY is required to boot the application for cache
# generation. It is a dummy value — the real key is injected at runtime via
# environment variables or a secret manager.
# 43 A's + '=' decodes to exactly 32 bytes, satisfying AES-256-CBC requirements.
ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
ENV APP_ENV=production

RUN php artisan package:discover --ansi \
    && php artisan route:cache \
    && php artisan event:cache \
    && php artisan view:cache

RUN chown -R www-data: /var/www/storage /var/www/bootstrap/cache

FROM ghcr.io/specsnl/php[[ .PhpVersion | replace "."  "" ]]:runtime-[[ $dockerTag ]] AS runtime

COPY --link app/ /var/www/app/
COPY --link config/ /var/www/config/
COPY --link database/migrations/ /var/www/database/migrations/
COPY --link routes/ /var/www/routes/
COPY --link resources/views/ /var/www/resources/views/
COPY --link storage/ /var/www/storage/
COPY --link artisan /var/www/artisan
COPY --link bootstrap/app.php bootstrap/providers.php /var/www/bootstrap/

COPY --link --from=deps /var/www/public /var/www/public/

COPY --from=prod-precompile /var/www/vendor /var/www/vendor/
COPY --from=prod-precompile /var/www/bootstrap/cache /var/www/bootstrap/cache/
COPY --from=prod-precompile /var/www/storage/framework /var/www/storage/framework/

COPY --link ./php/files/ /

RUN chown -R www-data: /var/www/storage /var/www/bootstrap/cache

ENTRYPOINT ["docker-php-entrypoint", "runtime-entrypoint.sh"]
CMD ["docker-cmd.sh"]
