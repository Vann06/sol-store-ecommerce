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
        // Registrar alias de middleware
        $middleware->alias([
            // Forzar que 'auth' use nuestro middleware que no redirige a rutas web inexistentes
            'auth' => \App\Http\Middleware\Authenticate::class,
            'optional.sanctum' => \App\Http\Middleware\OptionalSanctumAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
