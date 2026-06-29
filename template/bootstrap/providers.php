<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
[[- if .AddFilament ]]
use App\Providers\Filament\DashboardPanelProvider;
[[- end ]]
[[- if .AddBugSnag ]]
use Bugsnag\BugsnagLaravel\BugsnagServiceProvider;
[[- end ]]

return [
    [[- if .AddBugSnag ]]
    BugsnagServiceProvider::class,
    [[- end ]]
    AppServiceProvider::class,
    [[- if .AddFilament ]]
    DashboardPanelProvider::class,
    [[- end ]]
];
