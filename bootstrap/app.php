<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\UsertypeMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()->dashboardUrl());

        $middleware->web(append: [
            PreventBackHistory::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
            'usertype' => UsertypeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($exception->getStatusCode() !== 419 || ! ($exception->getPrevious() instanceof TokenMismatchException)) {
                return null;
            }

            $isLoginOrProtectedRoute = $request->is('login') || in_array('auth', $request->route()?->gatherMiddleware() ?? [], true);
            if (! $isLoginOrProtectedRoute) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Halaman sudah kedaluwarsa. Muat ulang halaman dan coba lagi.',
                ], 419);
            }

            if ($user = $request->user()) {
                return redirect($user->dashboardUrl())
                    ->with('expired_form', 'Formulir sudah kedaluwarsa. Perubahan belum disimpan. Silakan buka ulang formulir.');
            }

            return redirect()
                ->route('login')
                ->with('status', 'Sesi sudah berakhir. Silakan login kembali.');
        });
    })->create();
