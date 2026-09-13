<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
        ]);
        $middleware->redirectTo(
            guests: function ($request) {
                if ($request->is('admin*')) {
                    return route('admin.login');
                }
                if ($request->is('student*')) {
                    return route('student.login');
                }
                return route('login');
            },
            users: function ($request) {
                if (\Illuminate\Support\Facades\Auth::guard('student')->check()) {
                    return route('student.dashboard');
                }
                return route('admin.dashboard');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
