<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\UsertypeMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            PreventBackHistory::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
            'usertype' => UsertypeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi sudah berakhir. Silakan login kembali.',
                ], 419);
            }

            return redirect()
                ->route('login')
                ->with('status', 'Sesi sudah berakhir. Silakan login kembali.');
        });
    })->create();
