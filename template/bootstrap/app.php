<?php

declare(strict_types=1);

[[ if .AddBugSnag ]]use Bugsnag\BugsnagLaravel\OomBootstrapper;
[[ end -]]
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

[[if .AddBugSnag ]](new OomBootstrapper())->bootstrap();

[[ end -]]

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
[[- if .UseFrankenphp ]]
        // The private_ranges of trusted_proxies in the Caddyfile, where the reverse proxy in front sets the X-Forwarded-* headers
        $middleware->trustProxies(at: ['192.168.0.0/16', '172.16.0.0/12', '10.0.0.0/8', '127.0.0.1/8', 'fd00::/8', '::1']);
[[- else ]]
        //
[[- end ]]
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
