<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
[[- if .AddBugSnag ]]
use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
[[- end ]]

return [
    [[- if .AddBugSnag ]]
    BugsnagServiceProvider::class,
    [[- end ]]
    AppServiceProvider::class,
];
