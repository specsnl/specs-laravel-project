# syntax=docker/dockerfile:1

# The development image: compose mounts the source into it
FROM [[ .PhpImage ]]/builder:[[ .PhpDockerTag ]] AS dev

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

# The production Composer packages, without the autoloader: app-build generates it
FROM [[ .PhpImage ]]/builder:[[ .PhpDockerTag ]] AS vendor

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
FROM [[ .PhpImage ]]/builder:[[ .PhpDockerTag ]] AS assets

COPY --link ./package.json ./pnpm-lock.yaml ./pnpm-workspace.yaml /var/www/

RUN --mount=type=cache,target=/cache \
    --mount=type=cache,target=/data/pnpm/store \
    pnpm install --frozen-lockfile

COPY --link ./vite.config.js /var/www/
COPY --link ./resources /var/www/resources/
COPY --link --from=vendor /var/www/vendor /var/www/vendor/

RUN pnpm run build

# The application files of the runtime image, with the autoloader and the artisan caches
FROM [[ .PhpImage ]]/builder:[[ .PhpDockerTag ]] AS app-build

ENV APP_ENV=production

COPY --link --from=vendor /var/www/vendor /var/www/vendor/

COPY --link ./composer.json ./artisan /var/www/
COPY --link ./app /var/www/app/
COPY --link ./bootstrap /var/www/bootstrap/
COPY --link ./config /var/www/config/
COPY --link ./database/migrations /var/www/database/migrations/
COPY --link ./public /var/www/public/
COPY --link ./resources/views /var/www/resources/views/
COPY --link ./routes /var/www/routes/
COPY --link ./storage /var/www/storage/

# Also runs the Composer scripts, such as package:discover
RUN composer dump-autoload \
        --no-dev \
        --classmap-authoritative

# A placeholder APP_KEY is required to boot the application for cache
# generation. It is a dummy value — the real key is injected at runtime via
# environment variables or a secret manager.
# 43 A's + '=' decodes to exactly 32 bytes, satisfying AES-256-CBC requirements.
RUN export APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
    && php artisan route:cache \
    && php artisan event:cache \
    && php artisan view:cache
[[- if .UseFrankenphp ]]

# FrankenPHP serves public/ itself, so the uploads are served through this link
RUN ln -s /var/www/storage/app/public /var/www/public/storage
[[- end ]]

# The production image
FROM [[ .PhpImage ]]:[[ .PhpDockerTag ]] AS runtime

COPY --link --from=app-build --exclude=storage --exclude=bootstrap/cache /var/www/ /var/www/
COPY --link --from=assets /var/www/public/build/ /var/www/public/build/
COPY --from=app-build --chown=www-data:www-data /var/www/storage/ /var/www/storage/
COPY --from=app-build --chown=www-data:www-data /var/www/bootstrap/cache/ /var/www/bootstrap/cache/

COPY --link ./php/files/ /

ENTRYPOINT ["docker-php-entrypoint", "runtime-entrypoint.sh"]
CMD ["docker-cmd.sh"]
