#!/usr/bin/env bash

set -eu
[[- if .UseFrankenphp ]]

echo "Starting FrankenPHP..."
exec frankenphp run --config /etc/frankenphp/Caddyfile --adapter caddyfile
[[- else ]]

echo "Starting php-fpm..."
exec php-fpm
[[- end ]]
