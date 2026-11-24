#!/usr/bin/env bash

set -eu

# Config cache is environment-specific (embeds secrets/urls) and must be
# generated at runtime with the actual environment values.
echo "Caching config for runtime environment..."
php artisan config:cache

# Route registration can depend on the config too (Filament's MFA routes, for instance),
# so the routes are cached with the actual environment values as well.
echo "Caching routes for runtime environment..."
php artisan route:cache

exec "$@"
