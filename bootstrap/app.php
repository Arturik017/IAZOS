<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ alias-uri middleware (admin, checkout etc.)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'checkout.auth' => \App\Http\Middleware\CheckoutAuthToRegister::class,
        ]);

        // ✅ exceptii CSRF
        $middleware->validateCsrfTokens(except: [
            'pay/maib/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
