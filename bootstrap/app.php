<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {

            $centralDomains = config('tenancy.central_domains');

            foreach ($centralDomains as $domain) {
                Route::middleware(['auth:api', 'universal'])
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }

            Route::middleware([
                'auth:api',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ])
            ->prefix('/api/v1')
            ->group(base_path('routes/tenant.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
