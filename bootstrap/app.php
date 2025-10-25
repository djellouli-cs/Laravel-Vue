<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectUnapprovedUser;
use App\Http\Middleware\EnsureUserIsStandard; // ✅ Add this

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Register your custom middleware aliases here
        $middleware->alias([
            'approvedOnly' => RedirectUnapprovedUser::class,
            'standard' => EnsureUserIsStandard::class, // ✅ Add this line
        ]);

        // Add web-specific middleware
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
