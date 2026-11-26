<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'api.log' => \App\Http\Middleware\LogApiAccess::class,
            'ability' => \App\Http\Middleware\CheckTokenAbility::class,
            'api.client' => \App\Http\Middleware\CheckApiClientIp::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Respons error aset Swagger UI tidak boleh disimpan di cache.
        $exceptions->respond(function (SymfonyResponse $response, \Throwable $e, Request $request): SymfonyResponse {
            if (str_starts_with($request->path(), 'docs/asset/') && $response->getStatusCode() >= 400) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->remove('Expires');
                $response->setVary('Accept-Encoding');
            }

            return $response;
        });
    })->create();
