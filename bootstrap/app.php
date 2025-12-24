<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\CheckTenantAdmin; // <--- 1. IMPORT CLASS SATPAMNYA
use App\Http\Middleware\PreventBackHistory; // <--- IMPORT CLASS PREVENT BACK HISTORY

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ini settingan Inertia lu yang lama (Biarin aja)
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // 👇 2. MASUKIN DAFTAR ALIAS DISINI CUK!
        $middleware->alias([
            'tenant.admin' => CheckTenantAdmin::class,
            'prevent-back-history' => PreventBackHistory::class,

        ]);
        $middleware->redirectTo(
            guests: '/login',      
            users: '/dashboard'    // <--- Pastiin ini sesuai nama route dashboard lu
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();