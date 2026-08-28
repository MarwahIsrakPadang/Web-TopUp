<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\EventServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (TokenMismatchException $e, \Illuminate\Http\Request $request) {
            logger()->warning('CSRF token mismatch', [
                'url' => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'session_cookie' => $request->hasCookie(config('session.cookie')),
                'has_xsrf_cookie' => $request->hasCookie('XSRF-TOKEN'),
                'has_csrf_header' => $request->headers->has('X-CSRF-TOKEN'),
                'has_xsrf_header' => $request->headers->has('X-XSRF-TOKEN'),
            ]);
        });
    })->create();
