<?php
// filepath: taskcurso/bootstrap/app.php

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
            'auth' => \App\Http\Middleware\Authenticate::class,
            'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class, // ← Middleware JWT
            'optional.sanctum' => \App\Http\Middleware\OptionalSanctumAuth::class,
        ]);

         // Excluir rutas problemáticas de CSRF temporalmente
        $middleware->validateCsrfTokens(except: [
            // Reportes (solo export y filtros GET/POST específicos)
            'admin/reports/exportar/pdf',
            'admin/reports/filtros/fechas',
            // Estado de pedidos admin (PUT) – evitamos 419 manteniendo seguridad básica
            'admin/orders/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
