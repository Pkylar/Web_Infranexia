<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // <- tambah ini

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust header proxy dari Railway (penting agar URL dianggap HTTPS)
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                   | Request::HEADER_X_FORWARDED_HOST
                   | Request::HEADER_X_FORWARDED_PORT
                   | Request::HEADER_X_FORWARDED_PROTO
                   | Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Alias middleware kamu tetap ada
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
