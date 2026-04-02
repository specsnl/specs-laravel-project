#!/usr/bin/env bash

set -eu

# Config cache is environment-specific (embeds secrets/urls) and must be
# generated at runtime with the actual environment values.
echo "Caching config for runtime environment..."
php artisan config:cache

exec "$@"
