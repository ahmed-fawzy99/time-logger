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
        then: function ($router) {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/apiVersions/v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON response
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $statusCode = 500;
                switch (get_class($e)) {
                    case \Illuminate\Validation\ValidationException::class:
                        $statusCode = 422;
                        break;
                    case \Symfony\Component\HttpFoundation\Exception\BadRequestException::class:
                    case ExistingData::class:
                        $statusCode = 400;
                        break;
                    case \Illuminate\Auth\AuthenticationException::class:
                        $statusCode = 401;
                        break;
                    case \Illuminate\Auth\Access\AuthorizationException::class:
                    case \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class:
                        $statusCode = 403;
                        break;
                    case \Illuminate\Database\Eloquent\ModelNotFoundException::class:
                        $statusCode = 404;
                        break;
                    case \Illuminate\Http\Exceptions\ThrottleRequestsException::class:
                        $statusCode = 429;
                        break;
                }

                return response()->json([
                    'status' => $statusCode,
                    'message' => $e->getMessage() ?: 'An error occurred',
                    // Send errors only in development or if it's a validation error. None otherwise
                    'errors' => (method_exists($e, 'errors') &&
                        (! app()->environment('production') || $statusCode == 422))
                        ? $e->errors()
                        : [],
                ], $statusCode);
            }

            return null;
        });
    })->create();
