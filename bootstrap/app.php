<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withBindings([
        // Register CartService as a singleton
        App\Services\CartService::class => function ($app) {
            return new App\Services\CartService;
        },
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
