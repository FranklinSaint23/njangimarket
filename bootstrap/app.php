<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render (et tout reverse proxy) envoie X-Forwarded-Proto: https
        // Sans ça, Laravel génère des URLs http:// et le CSS/JS est bloqué
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role'  => \App\Http\Middleware\CheckRole::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
        $middleware->validateCsrfTokens(except: ['webhook/campay']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
